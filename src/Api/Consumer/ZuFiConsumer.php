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
use App\Entity\FederalInformationManagementType;
use App\Entity\Repository\CommuneRepository;
use App\Entity\Service;
use App\Entity\StateGroup\Commune;
use App\Import\Model\ResultCollection;

class ZuFiConsumer extends AbstractApiConsumer
{
    public const DEFAULT_REGIONAL_KEY = '0500000000000';

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
     * Import commune data
     * @param int $limit Limit the number of rows to be imported
     * @param array $serviceKeys Optional list of service keys to be imported
     * @param string $sorting
     * @param bool $forceUpdate
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function importCommuneServiceResults(int $limit = 500, array $serviceKeys = [], string $sorting = 'random', bool $forceUpdate = false): int
    {
        /** @var CommuneRepository $repository */
        $em = $this->getEntityManager();
        $em->getConfiguration()->setSQLLogger(null);
        $repository = $em->getRepository(Commune::class);
        $queryBuilder = $repository->createQueryBuilder('c');
        $queryBuilder->select('c.id');
        if ($sorting === 'modified') {
            $queryBuilder->orderBy('c.modifiedAt', 'DESC');
        } else {
            $queryBuilder->orderBy('c.id', 'ASC');
        }
        $result = $queryBuilder->getQuery()->getArrayResult();
        if (!empty($result)) {
            $idList = array_column($result, 'id');
        } else {
            $idList = [0];
        }
        // Randomize order of communes
        if ($sorting === 'random') {
            shuffle($idList);
        } elseif ($sorting === 'modified') {
            $idList = array_slice($idList, 0, min(ceil($limit/200), count($idList)));
            shuffle($idList);
        }
        $totalImportRowCount = 0;
        foreach ($idList as $id) {
            $commune = $repository->find($id);
            /** @var Commune|null $commune */
            if (null !== $commune && $commune->getRegionalKey()) {
                echo 'Importing commune service results: ' . $commune->getName() . ' [ID: '.$commune->getId().'][Modified at: '.(null !== $commune->getModifiedAt() ? $commune->getModifiedAt()->format('Y-m-d H:i:s') : '-').']' . "\n";
                $totalImportRowCount += $this->importServiceResults($limit, $commune, $serviceKeys, $forceUpdate);
                ++$totalImportRowCount;
                if ($totalImportRowCount > $limit) {
                    break;
                }
                $em->clear();
            }
        }
        return $totalImportRowCount;
    }

    /**
     * Import service data
     * @param int $limit Limit the number of rows to be imported
     * @param Commune|int|null $commune
     * @param array $serviceKeys Optional list of service keys to be imported
     * @param bool $forceUpdate
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function importServiceResults(
        int $limit,
        ?Commune $commune,
        array $serviceKeys = [],
        bool $forceUpdate = false
    ): int
    {
        $demand = $this->getDemand();
        /** @var ZuFiDemand $demand */
        if (null !== $commune) {
            $demand->setRegionalKey($commune->getRegionalKey());
        }
        if (!$demand->getZipCode() && !$demand->getRegionalKey()) {
            throw new InvalidParametersException('The demand parameters are not set. You must set the zip code or regional key in the demand!');
        }
        $mapToFimType = null;
        if ($demand->getRegionalKey() === self::DEFAULT_REGIONAL_KEY) {
            $mapToFimType = FederalInformationManagementType::TYPE_DESCRIPTION;
        }
        $validFimTypes = array_keys(FederalInformationManagementType::$mapTypes);
        if (null === $commune && (null === $mapToFimType || !in_array($mapToFimType, $validFimTypes, false))) {
            throw new InvalidParametersException(sprintf(
                'The given fim type %s is not valid. Valid values are %s',
                $mapToFimType ?? 'NULL',
                implode(', ', $validFimTypes)
            ));
        }
        $em = $this->getEntityManager();
        $em->getConfiguration()->setSQLLogger(null);
        $repository = $em->getRepository(Service::class);
        $services = $repository->findAll();
        $mapServicesByKey = [];
        $allServiceKeys = [];
        $mappedServiceKeys = [];
        foreach ($services as $service) {
            /** @var Service $service */
            $serviceKey = strtolower($service->getServiceKey());
            if ($serviceKey
                && strpos($serviceKey, 'nicht') === false
                && strpos($serviceKey, 'nciht') === false
                && strpos($serviceKey, 'unbekannt') === false
                && strpos($serviceKey, 'neu') === false
                && (empty($serviceKeys) || in_array($serviceKey, $serviceKeys, false))
            ) {
                $mapServicesByKey[$serviceKey] = $service;
                $mappedServiceKeys[$serviceKey] = $serviceKey;
            }
            $allServiceKeys[$serviceKey] = $serviceKey;
        }

        if ($forceUpdate || empty($serviceKeys)) {
            $this->removeOldEntries(
                $forceUpdate,
                !empty($serviceKeys),
                $commune,
                $allServiceKeys,
                $mappedServiceKeys
            );
        }
        $shuffledServices = [];
        // Order service mapping so oldest or new entries are imported first
        if (null !== $commune || $demand->getRegionalKey() === self::DEFAULT_REGIONAL_KEY) {
            $sbrRows = $this->fetchBaseResultRows($commune, $demand->getRegionalKey());
            $mapRowsByServiceKey = [];
            foreach ($sbrRows as $row) {
                $mapRowsByServiceKey[$row['service_key']] = $row['modified_at'] ?: $row['created_at'];
            }
            $unmappedKeys = array_diff($mappedServiceKeys, array_keys($mapRowsByServiceKey));
            $mappedServiceKeys = [];
            foreach ($unmappedKeys as $serviceKey) {
                $mappedServiceKeys[$serviceKey] = $serviceKey;
            }
            asort($mapRowsByServiceKey);
            foreach (array_keys($mapRowsByServiceKey) as $serviceKey) {
                $mappedServiceKeys[$serviceKey] = $serviceKey;
            }
            if (!empty($serviceKeys)) {
                $tmpServiceKeys = $mapRowsByServiceKey;
                $mapRowsByServiceKey = [];
                foreach ($serviceKeys as $serviceKey) {
                    if (isset($tmpServiceKeys[$serviceKey])) {
                        $mapRowsByServiceKey[$serviceKey] = $serviceKey;
                    }
                }
            }
        } else {
            shuffle($mappedServiceKeys);
        }
        foreach ($mappedServiceKeys as $serviceKey) {
            if (isset($mapServicesByKey[$serviceKey])) {
                $shuffledServices[$serviceKey] = $mapServicesByKey[$serviceKey];
            }
        }
        /** @var ZuFiDataProcessor $dataProcessor */
        $dataProcessor = $this->dataProcessor;
        $this->dataProvider->setApiConsumerEntity($this->getApiConsumerEntity());
        $dataProcessor->setImportModelClass($this->getImportModelClass());
        $dataProcessor->setOutput($this->output);
        $dataProcessor->setImportSource($this->getImportSourceKey());
        $totalImportRowCount = 0;
        foreach ($shuffledServices as $service) {
            /** @var Service $service */
            $serviceKey = $service->getServiceKey();
            $demand->setServiceKey($serviceKey);
            $this->dataProvider->setDemand($demand);
            $this->dataProvider->process($dataProcessor);
            /** @var ZuFiResultCollection $results */
            $results = $this->dataProcessor->getResultCollection();
            $results->setOzgService($service);
            $serviceModel = $results->getServiceBase();
            $dataProcessor->addServiceResult($service, $serviceModel, $results);
            ++$totalImportRowCount;
            if ($totalImportRowCount % 100 === 0) {
                $dataProcessor->processImportedServiceResults($demand->getRegionalKey(), $mapToFimType, $commune);
            }
            if ($totalImportRowCount >= $limit) {
                break;
            }
        }
        $dataProcessor->processImportedServiceResults($demand->getRegionalKey(), $mapToFimType, $commune);
        return $totalImportRowCount;
    }

    /**
     * Returns the service base result data
     *
     * @param Commune|null $commune Optional commine
     * @param ?string $regionalKey Optional regional key
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function fetchBaseResultRows(?Commune $commune, ?string $regionalKey = null): array
    {
        $query = 'SELECT id, service_key, created_at, modified_at FROM ozg_api_service_base_result WHERE ';
        if (null !== $commune) {
            $query .= ' commune_id = ' . $commune->getId();
        } elseif (null !== $regionalKey) {
            $query .= " regional_key = $regionalKey";
        } else {
            $query .= ' commune_id IS NULL';
        }
        return $this->fetchAllAssociative($query);
    }

    /**
     * Remove old entries after 2 weeks threshold
     * @param bool $forceUpdate
     * @param bool $hasServiceKeys
     * @param Commune|null $commune
     * @param array $allServiceKeys
     * @param array $mappedServiceKeys
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function removeOldEntries(
        bool $forceUpdate,
        bool $hasServiceKeys,
        ?Commune $commune,
        array $allServiceKeys,
        array &$mappedServiceKeys
    ) {
        $deleteItems = [];
        $sbrRows = $this->fetchBaseResultRows($commune);
        // Force update of user defined service key list
        $updateThreshold = ($forceUpdate || $hasServiceKeys) ? time() : strtotime('-2 weeks');
        foreach ($sbrRows as $sbrRow) {
            $sbrServiceKey = $sbrRow['service_key'];
            if (!in_array($sbrServiceKey, $allServiceKeys, false)) {
                $deleteItems[] = (int) $sbrRow['id'];
            } else {
                $lastUpdate = !empty($sbrRow['modified_at']) ? $sbrRow['modified_at'] : $sbrRow['created_at'];
                if (!$forceUpdate && null !== $lastUpdate && strtotime($lastUpdate) > $updateThreshold) {
                    unset($mappedServiceKeys[$sbrServiceKey]);
                }
            }
        }
        if (!empty($deleteItems)) {
            $sql = 'DELETE FROM ozg_api_service_base_result WHERE id IN (' . implode(', ', $deleteItems) . ')';
            $this->executeStatement($sql);
        }
    }

    /**
     * Execute a raw sql statement; used instead of Doctrine DQL for performance reasons
     *
     * @param string $sql
     * @throws \Doctrine\DBAL\Exception
     */
    protected function executeStatement(string $sql)
    {
        $connection = $this->getEntityManager()->getConnection();
        if (method_exists($connection, 'executeStatement')) {
            $connection->executeStatement($sql);
        } else {
            /** @noinspection PhpUnhandledExceptionInspection */
            $connection->executeUpdate($sql);
        }
    }

    /**
     * Execute a raw sql statement; used instead of Doctrine DQL for performance reasons
     *
     * @param string $sql
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fetchAllAssociative(string $sql)
    {
        $connection = $this->getEntityManager()->getConnection();
        if (method_exists($connection, 'fetchAllAssociative')) {
            return $connection->fetchAllAssociative($sql);
        }
        return $connection->fetchAll($sql);
    }

    /**
     * Searches for the submitted demand values and returns the search result
     *
     * @return ResultCollection The result content
     */
    public function search(): ResultCollection
    {
        $demand = $this->getDemand();
        $communeRepository = $this->getEntityManager()->getRepository(Commune::class);
        $commune = null;
        /** @var ZuFiDemand $demand */
        // Set either the zip code or the regional key from the commune entity, if one of the values is empty
        if (!$demand->getRegionalKey() && $demand->getZipCode()) {
            $commune = $communeRepository->findOneBy(['zipCode' => $demand->getZipCode()]);
            if (null !== $commune && $regionalKey = $commune->getRegionalKey()) {
                $demand->setRegionalKey($regionalKey);
            }
        } elseif ($demand->getRegionalKey() && !$demand->getZipCode()) {
            $commune = $communeRepository->findOneBy(['regionalKey' => $demand->getRegionalKey()]);
            if (null !== $commune && $zipCode = $commune->getZipCode()) {
                $demand->setZipCode($zipCode);
            }
        }
        if (null === $commune && $demand->getRegionalKey()) {
            $commune = $communeRepository->findOneBy(['regionalKey' => $demand->getRegionalKey()]);
        }
        /** @var ZuFiResultCollection $resultCollection */
        $resultCollection = parent::search();
        if ($commune instanceof Commune) {
            $this->updateOrCreateServiceBaseResult($commune, $resultCollection);
        }
        return $resultCollection;
    }

    /**
     * @param Commune $commune
     * @param ZuFiResultCollection $resultCollection
     * @return void
     */
    private function updateOrCreateServiceBaseResult(Commune $commune, ZuFiResultCollection $resultCollection): void
    {
        if ($serviceBaseResultModel = $resultCollection->getServiceBase()) {
            $serviceRepository = $this->getEntityManager()->getRepository(Service::class);
            $ozgService = $serviceRepository->findOneBy(['serviceKey' => $serviceBaseResultModel->getServiceKey()]);
            if ($ozgService) {
                $resultCollection->setOzgService($ozgService);
                /** @var ZuFiDataProcessor $dataProcessor */
                $dataProcessor = $this->dataProcessor;
                $dataProcessor->addServiceResult($ozgService, $serviceBaseResultModel, $resultCollection);
                try {
                    $dataProcessor->processImportedServiceResults(
                        $commune->getRegionalKey(),
                        null,
                        $commune
                    );
                } catch (\Exception $e) {
                    // Skip optional update of stored result
                    unset($e);
                }
            }
        }
    }
}
