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

namespace App\Api\Consumer;

use App\Api\Consumer\DataProcessor\ZuFiDataProcessor;
use App\Api\Consumer\Model\ZuFi\ZuFiResultCollection;
use App\Api\Consumer\Model\ZuFiDemand;
use App\Api\Consumer\Model\ZuFiResult;
use App\Api\Form\Type\ZuFiType;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Api\ServiceBaseResult;
use App\Entity\FederalInformationManagementType;
use App\Entity\Service;

class ZuFiConsumer extends AbstractApiConsumer
{
    use InjectManagerRegistryTrait;

    /**
     * @required
     * @param ZuFiDataProcessor $dataProcessor
     */
    public function injectDataProcessor(ZuFiDataProcessor $dataProcessor): void
    {
        $this->dataProcessor = $dataProcessor;
    }

    /**
     * Returns the class name for the result model
     *
     * @return string
     */
    protected function getDemandClass(): string
    {
        return ZuFiDemand::class;
    }

    /**
     * Returns the search result template for this consumer
     * @return string
     */
    public function getResultTemplate(): string
    {
        return 'Vsm/Partials/Results/_zu-fi-results.html.twig';
    }

    /**
     * Returns the class name for the search form type
     *
     * @return string
     */
    public function getFormTypeClass(): string
    {
        return ZuFiType::class;
    }

    /**
     * Returns the class name for the result model
     *
     * @return string
     */
    public function getImportModelClass(): string
    {
        return ZuFiResult::class;
    }

    /**
     * @param string|null $mapToFimType
     * @param int $limit Limit the number of rows to be imported
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function importServiceResults(?string $mapToFimType = null, int $limit = 100): int
    {
        $demand = $this->getDemand();
        /** @var ZuFiDemand $demand */
        if (!$demand->getZipCode() && !$demand->getRegionalKey()) {
            throw new InvalidParametersException('The demand parameters are not set. You must set the zip code or regional key in the demand!');
        }
        $validFimTypes = array_keys(FederalInformationManagementType::$mapTypes);
        if (null === $mapToFimType || !in_array($mapToFimType, $validFimTypes, false)) {
            throw new InvalidParametersException('The given fim type %s is not valid. Valid values are %s', $mapToFimType, implode(', ', $validFimTypes));
        }
        $repository = $this->getEntityManager()->getRepository(Service::class);
        $sbrRepository = $this->getEntityManager()->getRepository(ServiceBaseResult::class);
        $sbrRows = $sbrRepository->findAll();
        $serviceUpdateMap = [];
        $updateThreshold = strtotime('-2 weeks');
        foreach ($sbrRows as $sbrRow) {
            /** @var ServiceBaseResult $sbrRow */
            $lastUpdate = $sbrRow->getModifiedAt() ?? $sbrRow->getCreatedAt();
            $serviceUpdateMap[$sbrRow->getServiceKey()] = null !== $lastUpdate ? $lastUpdate->getTimestamp() : $updateThreshold;
        }
        $services = $repository->findAll();
        /** @var ZuFiDataProcessor $dataProcessor */
        $dataProcessor = $this->dataProcessor;
        $this->dataProvider->setApiConsumerEntity($this->getApiConsumerEntity());
        $dataProcessor->setImportModelClass($this->getImportModelClass());
        $dataProcessor->setOutput($this->output);
        $dataProcessor->setImportSource($this->getImportSourceKey());
        $totalImportRowCount = 0;
        foreach ($services as $service) {
            /** @var Service $service */
            $serviceKey = $service->getServiceKey();
            $lastUpdate = $serviceUpdateMap[$serviceKey] ?? null;
            if ($lastUpdate && $lastUpdate > $updateThreshold) {
                continue;
            }
            $demand->setServiceKey($serviceKey);
            $this->dataProvider->setDemand($demand);
            $this->dataProvider->process($dataProcessor);
            $results = $this->dataProcessor->getResultCollection();
            /** @var ZuFiResultCollection $results */
            $serviceModel = $results->getServiceBase();
            if (null !== $serviceModel && $serviceModel->getServiceKey()) {
                $dataProcessor->addServiceResult($service, $serviceModel);
                ++$totalImportRowCount;
                if ($totalImportRowCount % 100 === 0) {
                    $dataProcessor->processImportedServiceResults($mapToFimType);
                }
                if ($totalImportRowCount >= $limit) {
                    break;
                }
            }
        }
        $dataProcessor->processImportedServiceResults($mapToFimType);
        return $totalImportRowCount;
    }
}
