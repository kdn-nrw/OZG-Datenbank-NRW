<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Onboarding;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Onboarding\CommuneInfo;
use App\Entity\Onboarding\Epayment;
use App\Entity\Onboarding\FormSolution;
use App\Entity\Solution;
use App\Entity\StateGroup\Commune;

class OnboardingManager
{
    use InjectManagerRegistryTrait;

    /**
     * @var Solution
     */
    protected $solutionList;

    /**
     * Create onboarding entity items for all communes
     *
     * @param string $entityClass
     * @return int
     */
    public function createItems(string $entityClass): int
    {
        $createdRowCount = 0;
        $em = $this->getEntityManager();
        $infoRepository = $em->getRepository($entityClass);
        $infoRows = $infoRepository->findAll();

        $mappedIdList = [];
        foreach ($infoRows as $infoEntity) {
            if (null !== $commune = $infoEntity->getCommune()) {
                $mappedIdList[] = $commune->getId();
            }
        }
        $repository = $em->getRepository(Commune::class);
        $communes = $repository->findAll();
        foreach ($communes as $commune) {
            if (!in_array($commune->getId(), $mappedIdList, true)) {
                $infoEntity = new $entityClass($commune);
                $em->persist($infoEntity);
                ++$createdRowCount;
            }
        }
        $em->flush();
        if ($entityClass === FormSolution::class) {
            $this->updateContacts($entityClass);
        } elseif ($entityClass === CommuneInfo::class) {
            $this->updateContacts($entityClass);
        }
        $em->clear();
        return $createdRowCount;
    }

    private function updateContacts(string $entityClass)
    {
        $sql = null;
        if ($entityClass === FormSolution::class) {
            $sql = "UPDATE ozg_onboarding_contact c, ozg_onboarding fs, ozg_onboarding bi
            SET c.form_solution_id = fs.id, c.commune_id = bi.commune_id
            WHERE fs.record_type = 'formsolution'
            AND bi.record_type = 'communeinfo' AND c.commune_info_id = bi.id
            AND fs.commune_id = bi.commune_id AND c.form_solution_id IS NULL AND c.contact_type = 'fs'";
        } else {
            $sql = "UPDATE ozg_onboarding_contact c, ozg_onboarding fs, ozg_onboarding bi
            SET c.commune_info_id = bi.id, c.commune_id = bi.commune_id
            WHERE fs.record_type = 'formsolution'
            AND bi.record_type = 'communeinfo' AND c.commune_info_id IS NULL
            AND fs.commune_id = bi.commune_id AND c.form_solution_id = fs.id AND c.contact_type = 'fs'";
        }
        if ($sql) {
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

    public function updateAllOnboardingSolutions()
    {
        $this->updateAllCommuneInfoSolutions();
        $this->updateAllEpaymenServices();
    }

    private function updateAllCommuneInfoSolutions()
    {
        // Use SQL statements to increase performance
        $sql = "DELETE FROM ozg_onboarding_commune_solution WHERE solution_id NOT IN (SELECT id FROM ozg_solution WHERE enabled_municipal_portal = 1)";
        $this->executeStatement($sql);
        $em = $this->getEntityManager();
        if (null !== $configuration = $em->getConnection()->getConfiguration()) {
            $configuration->setSQLLogger(null);
        }
        $communeInfoRepository = $em->getRepository(CommuneInfo::class);
        $communeInfoResults = $communeInfoRepository->findAll();
        $connection = $this->getEntityManager()->getConnection();
        $query = 'SELECT id FROM ozg_solution WHERE enabled_municipal_portal = 1';
        $enabledSolutionIdList = $connection->fetchAllAssociative($query);
        if (!empty($enabledSolutionIdList)) {
            $enabledSolutionIdList = array_column($enabledSolutionIdList, 'id');
        }
        $mapReferencesToBeCreated = [];
        $now = date_create();
        $now->setTimezone(new \DateTimeZone('UTC'));
        $dateString = $now->format('Y-m-d H:i:s');
        foreach ($communeInfoResults as $offset => $communeInfo) {
            $referencesToBeCreated = $this->updateSingleCommuneSolution($communeInfo, $enabledSolutionIdList);
            if (!empty($referencesToBeCreated)) {
                $entityId = (int) $communeInfo->getId();
                $communeId = (int) $communeInfo->getCommune()->getId();
                foreach ($referencesToBeCreated as $solutionId) {
                    $mapReferencesToBeCreated[$entityId][$solutionId] = [
                        'commune_info_id' => $entityId,
                        'commune_id' => $communeId,
                        'solution_id' => $solutionId,
                        'enabled_epayment' => 0,
                        'enabled_municipal_portal' => 1,
                        'modified_at' => $dateString,
                        'created_at' => $dateString,
                    ];
                }
            }
        }
        $fieldTypes = [
            'commune_info_id' => '%d',
            'commune_id' => '%d',
            'solution_id' => '%d',
            'enabled_epayment' => '%d',
            'enabled_municipal_portal' => '%d',
            'modified_at' => '\'%s\'',
            'created_at' => '\'%s\'',
        ];
        $this->createReferences('ozg_onboarding_commune_solution', $mapReferencesToBeCreated, $fieldTypes);
    }

    private function updateAllEpaymenServices()
    {
        // Use SQL statements to increase performance
        $em = $this->getEntityManager();
        if (null !== $configuration = $em->getConnection()->getConfiguration()) {
            $configuration->setSQLLogger(null);
        }
        $ePaymentRepository = $em->getRepository(Epayment::class);
        $ePaymentResults = $ePaymentRepository->findAll();

        $now = date_create();
        $now->setTimezone(new \DateTimeZone('UTC'));
        $dateString = $now->format('Y-m-d H:i:s');
        $connection = $this->getEntityManager()->getConnection();
        $mapReferencesToBeCreated = [];
        foreach ($ePaymentResults as $offset => $ePayment) {
            $communeId = (int) $ePayment->getCommune()->getId();
            $query = 'SELECT solution_id FROM ozg_onboarding_commune_solution WHERE enabled_epayment = 1 AND commune_id = ' . $communeId;
            $enabledCommuneSolutionIdList = $connection->fetchAllAssociative($query);
            if (!empty($enabledCommuneSolutionIdList)) {
                $enabledCommuneSolutionIdList = array_column($enabledCommuneSolutionIdList, 'solution_id');
                $sql = "DELETE FROM ozg_onboarding_epayment_service WHERE solution_id NOT IN (".implode(', ', $enabledCommuneSolutionIdList).")";
                $this->executeStatement($sql);
                $referencesToBeCreated = $this->updateSingleEpaymentServices($ePayment, $enabledCommuneSolutionIdList);
                if (!empty($referencesToBeCreated)) {
                    $entityId = (int) $ePayment->getId();
                    foreach ($referencesToBeCreated as $solutionId) {
                        $mapReferencesToBeCreated[$entityId][$solutionId] = [
                            'epayment_id' => $entityId,
                            'solution_id' => $solutionId,
                            'hidden' => 0,
                            'modified_at' => $dateString,
                            'created_at' => $dateString,
                        ];
                    }
                }
            }
        }
        $em->flush();
        $em->clear();
        $fieldTypes = [
            'epayment_id' => '%d',
            'solution_id' => '%d',
            'hidden' => '%d',
            'modified_at' => '\'%s\'',
            'created_at' => '\'%s\'',
        ];
        $this->createReferences('ozg_onboarding_epayment_service', $mapReferencesToBeCreated, $fieldTypes);
    }

    /**
     * Save the given data in the table
     *
     * @param string $tableName
     * @param array $mapReferencesToBeCreated
     * @param array $fieldTypes
     * @throws \Doctrine\DBAL\Exception
     */
    private function createReferences(string $tableName, array $mapReferencesToBeCreated, array $fieldTypes): void
    {
        if (!empty($mapReferencesToBeCreated)) {
            $insert = 'INSERT INTO '.$tableName.' ('.implode(', ', array_keys($fieldTypes)).') VALUES ';
            $sqlStatements = [];
            $insertValueList = [];
            $count = 1;
            $placeholder = '('.implode(', ', $fieldTypes).')';
            foreach ($mapReferencesToBeCreated as $entityCreateRows) {
                foreach ($entityCreateRows as $values) {
                    $insertValueList[] = vsprintf($placeholder, $values);
                    if ($count > 500) {
                        $sqlStatements[] = $insert . implode(', ', $insertValueList);
                        $insertValueList = [];
                        $count = 0;
                    }
                    ++$count;
                }
            }
            if (!empty($insertValueList)) {
                $sqlStatements[] = $insert . implode(', ', $insertValueList);
            }
            foreach ($sqlStatements as $sql) {
                $this->executeStatement($sql);
            }
        }
    }

    /**
     * Update the service references for a single e-payment entity
     *
     * @param Epayment $ePayment
     * @param array<int, int> $enabledCommuneSolutionIdList
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateSingleEpaymentServices(Epayment $ePayment, array $enabledCommuneSolutionIdList): array
    {
        $em = $this->getEntityManager();
        $serviceSolutionMap = [];
        $removeServices = [];
        foreach ($ePayment->getEpaymentServices() as $ePaymentService) {
            if (null !== $mappedSolution = $ePaymentService->getSolution()) {
                // Solution does not exist (any more)
                if (!in_array($mappedSolution->getId(), $enabledCommuneSolutionIdList, false)) {
                    $removeServices[] = $ePaymentService;
                } else {
                    $serviceSolutionMap[$mappedSolution->getId()] = $ePaymentService;
                }
            }
        }
        foreach ($removeServices as $removeService) {
            $ePayment->removeEpaymentService($removeService);
            $em->remove($removeService);
        }
        $mappedSolutionIds = array_keys($serviceSolutionMap);
        $referencesToBeCreated = [];
        foreach ($enabledCommuneSolutionIdList as $entityId) {
            if (!in_array($entityId, $mappedSolutionIds, false)) {
                $referencesToBeCreated[] = (int) $entityId;
                /* Will cause out of memory
                $ePaymentService = new EpaymentService();
                $ePaymentService->setEpayment($ePayment);
                $ePaymentService->setSolution($entity);
                $em->persist($ePaymentService);*/
            }
        }
        return $referencesToBeCreated;
    }

    /**
     * Update the service references for a single e-payment entity
     *
     * @param CommuneInfo $communeInfo
     * @param array<int, int> $enabledSolutionIdList
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateSingleCommuneSolution(CommuneInfo $communeInfo, array $enabledSolutionIdList): array
    {
        $em = $this->getEntityManager();
        $serviceSolutionMap = [];
        $removeServices = [];
        foreach ($communeInfo->getCommuneSolutions() as $communeSolution) {
            if (null !== $mappedSolution = $communeSolution->getSolution()) {
                // Solution does not exist (any more)
                if (!in_array($mappedSolution->getId(), $enabledSolutionIdList, false)) {
                    $removeServices[] = $communeSolution;
                } else {
                    $serviceSolutionMap[$mappedSolution->getId()] = $communeSolution;
                }
            }
        }
        foreach ($removeServices as $removeService) {
            $communeInfo->removeCommuneSolution($removeService);
            $em->remove($removeService);
        }
        $mappedSolutionIds = array_keys($serviceSolutionMap);
        $referencesToBeCreated = [];
        foreach ($enabledSolutionIdList as $entityId) {
            if (!in_array($entityId, $mappedSolutionIds, false)) {
                $referencesToBeCreated[] = $entityId;
                /* Will cause out of memory
                $ePaymentService = new EpaymentService();
                $ePaymentService->setEpayment($ePayment);
                $ePaymentService->setSolution($entity);
                $em->persist($ePaymentService);*/
            }
        }
        return $referencesToBeCreated;
    }
}