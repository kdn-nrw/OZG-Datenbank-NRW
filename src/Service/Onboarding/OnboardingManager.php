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
use App\Entity\MetaData\CalculateCompletenessEntityInterface;
use App\Entity\Onboarding\AbstractOnboardingEntity;
use App\Entity\Onboarding\CommuneInfo;
use App\Entity\Onboarding\Epayment;
use App\Entity\Onboarding\EpaymentService;
use App\Entity\Onboarding\FormSolution;
use App\Entity\Onboarding\MonumentAuthority;
use App\Entity\Onboarding\PmPayment;
use App\Entity\Onboarding\PmPaymentService;
use App\Entity\Onboarding\XtaServer;
use App\Entity\StateGroup\Commune;
use Doctrine\DBAL\Exception\InvalidArgumentException;

class OnboardingManager
{
    use InjectManagerRegistryTrait;

    /**
     * @var OnboardingProgressCalculator
     */
    private $progressCalculator;

    /**
     * @required
     * @param OnboardingProgressCalculator $progressCalculator
     */
    public function injectOnboardingProgressCalculator(OnboardingProgressCalculator $progressCalculator): void
    {
        $this->progressCalculator = $progressCalculator;
    }

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
            if ($this->isValid($entityClass, $commune) && !in_array($commune->getId(), $mappedIdList, true)) {
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

    /**
     * Validate commune for given entity class; if false is returned, the onboarding reference is not created
     *
     * @param string $entityClass
     * @param Commune $commune
     * @return bool
     */
    private function isValid(string $entityClass, Commune $commune): bool
    {
        if ($entityClass === XtaServer::class) {
            $bureaus = $commune->getBureaus();
            foreach ($bureaus as $bureau) {
                if ($bureau->getId() === XtaServer::REQUIRED_COMMUNE_BUREAU_ID) {
                    return true;
                }
            }
            return false;
        }
        if ($entityClass === MonumentAuthority::class) {
            $bureaus = $commune->getBureaus();
            foreach ($bureaus as $bureau) {
                if ($bureau->getId() === MonumentAuthority::REQUIRED_COMMUNE_BUREAU_ID) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    private function updateContacts(string $entityClass)
    {
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
        $communeIdList = $this->updateAllCommuneInfoSolutions();
        $this->updateEpaymentServices();
        $this->updatePmPaymentServices();
        return count($communeIdList);
    }

    private function updateAllCommuneInfoSolutions()
    {
        // Use SQL statements to increase performance
        $sql = "DELETE FROM ozg_onboarding_commune_solution WHERE solution_id NOT IN (SELECT id FROM ozg_solution WHERE enabled_municipal_portal = 1)";
        $this->executeStatement($sql);
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        if (null !== $configuration = $connection->getConfiguration()) {
            $configuration->setSQLLogger(null);
        }
        $query = 'SELECT id, commune_id FROM ozg_onboarding WHERE record_type = \'communeinfo\' ORDER BY commune_id ASC';
        $rows = $this->fetchAllAssociative($query);
        $mapReferencesToBeCreated = [];
        $now = date_create();
        $now->setTimezone(new \DateTimeZone('UTC'));
        $dateString = $now->format('Y-m-d H:i:s');
        $communeIdList = [];
        foreach ($rows as $row) {
            $entityId = (int)$row['id'];
            $communeId = (int)$row['commune_id'];
            $communeIdList[$communeId] = $communeId;
            $referencesToBeCreated = $this->updateSingleCommuneSolution($communeId);
            if (!empty($referencesToBeCreated)) {
                foreach ($referencesToBeCreated as $solutionId) {
                    $mapReferencesToBeCreated[$entityId][$solutionId] = [
                        'commune_info_id' => $entityId,
                        'commune_id' => $communeId,
                        'solution_id' => $solutionId,
                        'enabled_epayment' => 0,
                        'enabled_pm_payment' => 0,
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
            'enabled_pm_payment' => '%d',
            'enabled_municipal_portal' => '%d',
            'modified_at' => '\'%s\'',
            'created_at' => '\'%s\'',
        ];
        $this->createReferences('ozg_onboarding_commune_solution', $mapReferencesToBeCreated, $fieldTypes);
        return $communeIdList;
    }

    /**
     * @param Commune|int $commune
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function updateEpaymentServices($commune = null)
    {
        $this->updateEntityCommuneSolutionReferences(
            Epayment::class,
            EpaymentService::class,
            'enabled_epayment',
            $commune
        );
    }

    /**
     * @param Commune|int|null $commune
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function updatePmPaymentServices($commune = null)
    {
        $this->updateEntityCommuneSolutionReferences(
            PmPayment::class,
            PmPaymentService::class,
            'enabled_pm_payment',
            $commune
        );
    }

    /**
     * @param string $entityClass The onboarding entity class
     * @param string $refEntityClass The entity class containing the references between
     * @param string $checkSolutionColumn The enable column to be checked in the onboarding solutions table
     * @param Commune|int|null $commune
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    private function updateEntityCommuneSolutionReferences(
        string $entityClass,
        string $refEntityClass,
        string $checkSolutionColumn,
        $commune = null
    ) {
        // Use SQL statements to increase performance
        $em = $this->getEntityManager();#
        $refClassMetaData = $em->getClassMetadata($refEntityClass);
        $refAssociations = $refClassMetaData->getAssociationsByTargetClass($entityClass);
        if (empty($refAssociations)) {
            throw new InvalidArgumentException(sprintf('There is no reference between %s and %s', $entityClass, $refEntityClass));
        }
        $refAssociation = current($refAssociations);
        $foreignKey = $refAssociation['joinColumns'][0]['name'];
        $refTableName = $em->getClassMetadata($refEntityClass)->getTableName();
        if (null !== $configuration = $em->getConnection()->getConfiguration()) {
            $configuration->setSQLLogger(null);
        }
        $repository = $em->getRepository($entityClass);
        if (null === $commune) {
            $results = $repository->findAll();
        } else {
            $results = $repository->findBy(['commune' => $commune]);
        }
        $now = date_create();
        $now->setTimezone(new \DateTimeZone('UTC'));
        $dateString = $now->format('Y-m-d H:i:s');
        $connection = $this->getEntityManager()->getConnection();
        $mapReferencesToBeCreated = [];
        foreach ($results as $entity) {
            $communeId = (int)$entity->getCommune()->getId();
            $entityId = (int)$entity->getId();
            $query = 'SELECT solution_id FROM ozg_onboarding_commune_solution WHERE '.$checkSolutionColumn.' = 1 AND commune_id = ' . $communeId;
            $enabledCommuneSolutionIdList = $connection->fetchAllAssociative($query);
            if (!empty($enabledCommuneSolutionIdList)) {
                $enabledCommuneSolutionIdList = array_column($enabledCommuneSolutionIdList, 'solution_id');
                //$sql = "SELECT * FROM ozg_onboarding_epayment_service WHERE epayment_id = $pmPaymentId AND solution_id NOT IN (".implode(', ', $enabledCommuneSolutionIdList).")";
                //$existingServices = $connection->fetchAllAssociative($sql);
                $sql = "DELETE FROM $refTableName WHERE $foreignKey = $entityId AND solution_id NOT IN (" . implode(', ', $enabledCommuneSolutionIdList) . ")";
                $this->executeStatement($sql);
                $referencesToBeCreated = $this->updateSingleEntityServices($entity, $enabledCommuneSolutionIdList);
                if (!empty($referencesToBeCreated)) {
                    foreach ($referencesToBeCreated as $solutionId) {
                        $mapReferencesToBeCreated[$entityId][$solutionId] = [
                            $foreignKey => $entityId,
                            'solution_id' => $solutionId,
                            'hidden' => 0,
                            'modified_at' => $dateString,
                            'created_at' => $dateString,
                        ];
                    }
                }
            } else {
                $sql = sprintf("DELETE FROM $refTableName WHERE $foreignKey = %d", $entity->getId());
                $this->executeStatement($sql);
            }
        }
        if (null === $commune) {
            $em->flush();
            $em->clear();
        }
        $fieldTypes = [
            $foreignKey => '%d',
            'solution_id' => '%d',
            'hidden' => '%d',
            'modified_at' => '\'%s\'',
            'created_at' => '\'%s\'',
        ];
        $this->createReferences($refTableName, $mapReferencesToBeCreated, $fieldTypes);
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
            $insert = 'INSERT INTO ' . $tableName . ' (' . implode(', ', array_keys($fieldTypes)) . ') VALUES ';
            $sqlStatements = [];
            $insertValueList = [];
            $count = 1;
            $placeholder = '(' . implode(', ', $fieldTypes) . ')';
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
                // Solution does not exist (anymore)
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
                $referencesToBeCreated[] = (int)$entityId;
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
     * @param AbstractOnboardingEntity $entity
     * @param array<int, int> $enabledCommuneSolutionIdList
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateSingleEntityServices(AbstractOnboardingEntity $entity, array $enabledCommuneSolutionIdList): array
    {
        if ($entity instanceof PmPayment) {
            return $this->updateSinglePmPaymentServices($entity, $enabledCommuneSolutionIdList);
        }
        if ($entity instanceof Epayment) {
            return $this->updateSingleEpaymentServices($entity, $enabledCommuneSolutionIdList);
        }
        return [];
    }

    /**
     * Update the service references for a single e-payment entity
     *
     * @param PmPayment $pmPayment
     * @param array<int, int> $enabledCommuneSolutionIdList
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateSinglePmPaymentServices(PmPayment $pmPayment, array $enabledCommuneSolutionIdList): array
    {
        $em = $this->getEntityManager();
        $serviceSolutionMap = [];
        $removeServices = [];
        foreach ($pmPayment->getPmPaymentServices() as $pmPaymentService) {
            if (null !== $mappedSolution = $pmPaymentService->getSolution()) {
                // Solution does not exist (anymore)
                if (!in_array($mappedSolution->getId(), $enabledCommuneSolutionIdList, false)) {
                    $removeServices[] = $pmPaymentService;
                } else {
                    $serviceSolutionMap[$mappedSolution->getId()] = $pmPaymentService;
                }
            }
        }
        foreach ($removeServices as $removeService) {
            $pmPayment->removePmPaymentService($removeService);
            $em->remove($removeService);
        }
        $mappedSolutionIds = array_keys($serviceSolutionMap);
        $referencesToBeCreated = [];
        foreach ($enabledCommuneSolutionIdList as $entityId) {
            if (!in_array($entityId, $mappedSolutionIds, false)) {
                $referencesToBeCreated[] = (int)$entityId;
                /* Will cause out of memory
                $pmPaymentService = new PmPaymentService();
                $pmPaymentService->setPmPayment($pmPayment);
                $pmPaymentService->setSolution($entity);
                $em->persist($pmPaymentService);*/
            }
        }
        return $referencesToBeCreated;
    }

    /**
     * Update the service references for a single commune info entity
     *
     * @param int $communeId
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateSingleCommuneSolution(int $communeId): array
    {
        $referencesToBeCreated = [];
        $connection = $this->getEntityManager()->getConnection();
        $query = sprintf('SELECT s.id FROM ozg_solutions_communes sc, ozg_solution s WHERE s.id = sc.solution_id AND s.enabled_municipal_portal = 1 AND sc.commune_id = %d', $communeId);
        $enabledCommuneSolutionIdList = $connection->fetchAllAssociative($query);
        if (!empty($enabledCommuneSolutionIdList)) {
            $enabledCommuneSolutionIdList = array_column($enabledCommuneSolutionIdList, 'id');
            $sql = sprintf("DELETE FROM ozg_onboarding_commune_solution WHERE commune_id = %d AND solution_id NOT IN (%s)",
                $communeId,
                implode(', ', $enabledCommuneSolutionIdList)
            );
            $this->executeStatement($sql);
            $query = sprintf('SELECT solution_id FROM ozg_onboarding_commune_solution WHERE commune_id = %d ORDER BY solution_id ASC', $communeId);
            $communeSolutionRows = $this->fetchAllAssociative($query);
            $mappedSolutionIds = [];
            if (!empty($communeSolutionRows)) {
                $mappedSolutionIds = array_column($communeSolutionRows, 'solution_id');
            }
            $referencesToBeCreated = array_diff($enabledCommuneSolutionIdList, $mappedSolutionIds);
        } else {
            $sql = sprintf("DELETE FROM ozg_onboarding_commune_solution WHERE commune_id = %d", $communeId);
            $this->executeStatement($sql);
        }
        return $referencesToBeCreated;
    }

    /**
     * Returns the information for the completion state
     *
     * @param CalculateCompletenessEntityInterface $object
     * @return array
     */
    public function getCompletionInfo(CalculateCompletenessEntityInterface $object): array
    {
        return $this->progressCalculator->getCompletionInfo($object);
    }

    /**
     * Common actions before entity is saved
     * @param AbstractOnboardingEntity $object
     */
    public function beforeSave(AbstractOnboardingEntity $object)
    {
        // Calculates the completion rate for this entity
        $this->progressCalculator->calculateCompletionRate($object);
    }

    /**
     * Common actions after entity is saved
     * @param AbstractOnboardingEntity $object
     */
    public function afterSave(AbstractOnboardingEntity $object)
    {
        if ($object instanceof CommuneInfo && null !== $commune = $object->getCommune()) {
            $this->updateEpaymentServices($commune);
            $this->updatePmPaymentServices($commune);
        }
    }
}