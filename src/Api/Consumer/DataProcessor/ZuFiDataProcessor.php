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

use App\Api\Consumer\Model\ZuFi\OrganisationResult;
use App\Api\Consumer\Model\ZuFi\ServiceBaseResult;
use App\Api\Consumer\Model\ZuFi\ServiceResult;
use App\Api\Consumer\Model\ZuFi\ZuFiResultCollection;
use App\Api\Consumer\Model\ZuFiResult;
use App\Entity\Api\ServiceBaseResult as ServiceBaseResultEntity;
use App\Entity\FederalInformationManagementType;
use App\Entity\Service;
use App\Entity\StateGroup\Commune;
use App\Import\Model\ResultCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

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

    /**
     * Adds the result for the given service to the list
     * @param Service $service
     * @param ?ServiceBaseResult $model
     * @param ZuFiResultCollection $resultCollection
     * @return void
     */
    public function addServiceResult(Service $service, ?ServiceBaseResult $model, ZuFiResultCollection $resultCollection): void
    {
        $this->serviceResults[] = [
            'service' => $service,
            'model' => $model,
            'resultCollection' => $resultCollection,
        ];
    }

    /**
     * Process content of the loaded import rows
     *
     * @param string $regionalKey
     * @param string|null $mapToFimType
     * @param Commune|null $commune
     * @return int The number of imported rows
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function processImportedServiceResults(
        string $regionalKey,
        ?string $mapToFimType,
        ?Commune $commune
    ): int
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $rowOffset = 0;
        $accessor = PropertyAccess::createPropertyAccessor();
        $modelEntityPropertyMapping = $this->getEntityPropertyMappingForModel(ServiceBaseResult::class);
        $targetEntityClass = current(array_keys($modelEntityPropertyMapping));
        $entityPropertyMapping = $modelEntityPropertyMapping[$targetEntityClass];
        $serviceModelEntityPropertyMapping = $this->getEntityPropertyMappingForModel(ServiceResult::class);
        $serviceTargetEntityClass = current(array_keys($serviceModelEntityPropertyMapping));
        $serviceEntityPropertyMapping = $serviceModelEntityPropertyMapping[$serviceTargetEntityClass];
        $sbrRepository = $em->getRepository(ServiceBaseResultEntity::class);
        foreach ($this->serviceResults as $dataRow) {
            $service = $dataRow['service'];
            $importModel = $dataRow['model'];
            $resultCollection = $dataRow['resultCollection'];
            if (null === $importModel) {
                $importModel = new ServiceBaseResult();
                $importModel->setName($service->getName());
                $importModel->setServiceKey($service->getServiceKey());
                $importModel->setDescription($service->getDescription());
            }
            /**
             * @var Service $service
             * @var ServiceBaseResult $importModel
             * @var ZuFiResultCollection $resultCollection
             */
            $serviceKey = $service->getServiceKey();
            $targetEntity = null;
            $fimEntity = null;
            if (!empty($mapToFimType)) {
                $fimEntity = $service->getFimType($mapToFimType);
                if (null === $fimEntity || !$em->contains($fimEntity)) {
                    $fimEntity = new FederalInformationManagementType();
                    $fimEntity->setStatus(FederalInformationManagementType::STATUS_IN_PROGRESS);
                    $fimEntity->setDataType($mapToFimType);
                    $fimEntity->setService($service);
                    $em->persist($fimEntity);
                } else {
                    $targetEntity = null;
                    $sbrEntity = $fimEntity->getServiceBaseResult();
                    if (null !== $targetEntity && $targetEntity->getCommune() !== $commune) {
                        $fimEntity->setServiceBaseResult(null);
                        if (null !== $sbrEntity) {
                            $sbrEntity->setFimType(null);
                        }
                    } else {
                        $targetEntity = $sbrEntity;
                    }
                    // Fallback in case service base result is not set in FIM entity
                    if (null === $targetEntity) {
                        $targetEntity = $sbrRepository->findOneBy([
                            'service' => $service->getId(),
                            'fimType' => $fimEntity->getId(),
                            'commune' => $commune
                        ]);
                    }
                }
            }
            if (null === $targetEntity && null !== $commune) {
                $targetEntity = $sbrRepository->findOneBy([
                    'service' => $service->getId(),
                    'commune' => $commune->getId()
                ]);
            }
            if (null === $targetEntity) {
                $targetEntity = new ServiceBaseResultEntity();
                $targetEntity->setImportSource($this->importSource);
                $targetEntity->setImportId($service->getId());
                $em->persist($targetEntity);
            }
            if (null !== $fimEntity) {
                $fimEntity->setServiceBaseResult($targetEntity);
            }
            $this->mapImportProperties($accessor, $entityPropertyMapping, $targetEntity, $importModel);
            if ($commune) {
                $commune->addServiceBaseResult($targetEntity);
                $targetEntity->setImportSource($this->importSource . '_c' . $commune->getId());
                $targetEntity->setCommuneHasDetails(false);
                if (!$resultCollection->isEmpty()) {
                    /** @var ZuFiResult $firstResult */
                    $firstResult = $resultCollection->first();
                    if (($firstResult instanceof ZuFiResult) && $communeServiceDetails = $firstResult->getService()) {
                        $targetEntity->setCommuneHasDetails(true);
                        // Mke sure the values of the base result are not overridden with empty values
                        foreach ($entityPropertyMapping as $modelProperty) {
                            if (in_array($modelProperty, $serviceEntityPropertyMapping, false)
                                && !$accessor->getValue($communeServiceDetails, $modelProperty)
                                && !!($baseValue = $accessor->getValue($importModel, $modelProperty))) {
                                $accessor->setValue($communeServiceDetails, $modelProperty, $baseValue);
                            }
                        }
                        $this->mapImportProperties($accessor, $serviceEntityPropertyMapping, $targetEntity, $communeServiceDetails);
                    }
                    if ($firstOrganisation = $firstResult->getOrganisations()->first()) {
                        /** @var OrganisationResult $firstOrganisation */
                        $targetEntity->setCommuneOfficeName($firstOrganisation->getName());
                    }
                }
            }
            $targetEntity->setService($service);
            $targetEntity->setServiceKey($serviceKey);
            $serviceCreatedAt = $targetEntity->getConvertedDate();
            $targetEntity->setServiceCreatedAt($serviceCreatedAt);
            $targetEntity->setRegionalKey($regionalKey);
            $targetEntity->setCommune($commune);
            $targetEntity->setFimType($fimEntity);
            ++$rowOffset;
            if ($rowOffset % 100 === 0) {
                $em->flush();
            }
        }
        $em->flush();
        $this->serviceResults = [];
        return $rowOffset;
    }

    /**
     * @param PropertyAccessor $accessor The property accessor
     * @param array $entityPropertyMapping<string, string> The mapping of the entity and model properties
     * @param ServiceBaseResultEntity $targetEntity
     * @param ServiceBaseResult $importModel
     * @return void
     * @throws \ReflectionException
     */
    protected function mapImportProperties(
        PropertyAccessor $accessor,
        array $entityPropertyMapping,
        ServiceBaseResultEntity $targetEntity,
        ServiceBaseResult $importModel): void
    {
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
    }
}
