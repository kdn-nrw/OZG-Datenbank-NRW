<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api\Consumer\DataProcessor;

use App\Api\Consumer\Model\ZuFi\ServiceBaseResult;
use App\Api\Consumer\Model\ZuFi\ZuFiResultCollection;
use App\Entity\FederalInformationManagementType;
use App\Entity\Service;
use App\Import\Model\ResultCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ZuFiDataProcessor extends DefaultApiDataProcessor
{
    /**
     * @var array
     */
    protected $serviceResults = [];

    /**
     * Initialize the result collection
     * @return ResultCollection|ZuFiResultCollection
     */
    public function getResultCollection(): ResultCollection
    {
        if (null === $this->resultCollection) {
            $this->resultCollection = new ZuFiResultCollection();
        }
        return $this->resultCollection;
    }

    public function addServiceResult(Service $service, ServiceBaseResult $model)
    {
        $this->serviceResults[] = [
            'service' => $service,
            'model' => $model,
        ];
    }

    /**
     * Process content of the loaded import rows
     * @param string|null $mapToFimType
     * @return int The number of imported rows
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function processImportedServiceResults(?string $mapToFimType = null): int
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $rowOffset = 0;
        $this->setImportModelClass(ServiceBaseResult::class);
        $accessor = PropertyAccess::createPropertyAccessor();
        $modelEntityPropertyMapping = $this->getModelEntityPropertyMapping();
        $targetEntityClass = current(array_keys($modelEntityPropertyMapping));
        $entityPropertyMapping = $modelEntityPropertyMapping[$targetEntityClass];
        $sbrRepository = $em->getRepository(\App\Entity\Api\ServiceBaseResult::class);
        foreach ($this->serviceResults as $dataRow) {
            $service = $dataRow['service'];
            $importModel = $dataRow['model'];
            /**
             * @var Service $service
             * @var ServiceBaseResult $importModel
             */
            $serviceKey = $service->getServiceKey();
            $fimEntity = $service->getFimType($mapToFimType);
            if (null === $fimEntity || !$em->contains($fimEntity)) {
                $targetEntity = null;
                $fimEntity = new FederalInformationManagementType();
                $fimEntity->setStatus(FederalInformationManagementType::STATUS_IN_PROGRESS);
                $fimEntity->setDataType($mapToFimType);
                $fimEntity->setService($service);
                $em->persist($fimEntity);
            } else {
                $targetEntity = $fimEntity->getServiceBaseResult();
                // Fallback in case service base result is not set in FIM entity
                if (null === $targetEntity) {
                    $targetEntity = $sbrRepository->findOneBy([
                        'service' => $service->getId(),
                        'fimType' => $fimEntity->getId()
                    ]);
                }
            }
            if (null === $targetEntity) {
                $targetEntity = new \App\Entity\Api\ServiceBaseResult();
                $targetEntity->setService($service);
                $targetEntity->setFimType($fimEntity);
                $targetEntity->setServiceKey($serviceKey);
                $targetEntity->setImportSource($this->importSource);
                $targetEntity->setImportId($service->getId());
                $em->persist($targetEntity);
            }
            $fimEntity->setServiceBaseResult($targetEntity);
            foreach ($entityPropertyMapping as $entityProperty => $modelProperty) {
                if ($accessor->isWritable($targetEntity, $entityProperty)) {
                    $value = $accessor->getValue($importModel, $modelProperty);
                    if ($value instanceof ResultCollection) {
                        $processedValue = $this->convertCollectionToArray($value);
                        $accessor->setValue($targetEntity, $entityProperty, $processedValue);
                    } else {
                        $accessor->setValue($targetEntity, $entityProperty, $value);
                    }
                }
            }
            ++$rowOffset;
            if ($rowOffset % 100 === 0) {
                $em->flush();
            }
        }
        $em->flush();
        $this->serviceResults = [];
        return $rowOffset;
    }
}
