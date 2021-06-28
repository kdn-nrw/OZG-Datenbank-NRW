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
        if ($entityClass === FormSolution::class || $entityClass === CommuneInfo::class) {
            $this->updateContacts($entityClass);
        }
        $em->clear();
        return $createdRowCount;
    }

    private function updateContacts(string $entityClass)
    {
        $sql = null;
        if ($entityClass === FormSolution::class || $entityClass === CommuneInfo::class) {
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

    public function updateAllEpaymenServices()
    {
        $em = $this->getEntityManager();
        if (null !== $configuration = $em->getConnection()->getConfiguration()) {
            $configuration->setSQLLogger(null);
        }
        $ePaymentRepository = $em->getRepository(Epayment::class);
        $ePaymentResults = $ePaymentRepository->findAll();

        $onboardingSolutions = $this->getOnboardingSolutionList();
        $mapReferencesToBeCreated = [];
        foreach ($ePaymentResults as $offset => $ePayment) {
            $referencesToBeCreated = $this->updateSingleEpaymentServices($ePayment, $onboardingSolutions);
            if (!empty($referencesToBeCreated)) {
                $mapReferencesToBeCreated[$ePayment->getId()] = $referencesToBeCreated;
            }
        }
        $em->flush();
        $em->clear();
        if (!empty($mapReferencesToBeCreated)) {
            $insert = 'INSERT INTO ozg_onboarding_epayment_service (epayment_id, solution_id, hidden) VALUES ';
            $sqlStatements = [];
            $insertValueList = [];
            $count = 1;
            foreach ($mapReferencesToBeCreated as $ePaymentId => $solutionIdLIst) {
                foreach ($solutionIdLIst as $solutionId) {
                    $insertValueList[] = sprintf('(%d, %d, %d)', $ePaymentId, $solutionId, 0);
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
     * @param array|null $onboardingSolutions
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function updateSingleEpaymentServices(Epayment $ePayment, ?array $onboardingSolutions = null): array
    {
        if (null === $onboardingSolutions) {
            $onboardingSolutions = $this->getOnboardingSolutionList();
        }
        $em = $this->getEntityManager();
        $serviceSolutionMap = [];
        $removeServices = [];
        foreach ($ePayment->getEpaymentServices() as $ePaymentService) {
            if (null !== $mappedSolution = $ePaymentService->getSolution()) {
                // Solution does not exist (any more)
                if (!array_key_exists($mappedSolution->getId(), $onboardingSolutions)) {
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
        foreach ($onboardingSolutions as $entity) {
            if (!in_array($entity->getId(), $mappedSolutionIds, false)) {
                $referencesToBeCreated[] = $entity->getId();
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
     * Returns the list of onboarding solutions
     *
     * @return array|Solution[]
     */
    protected function getOnboardingSolutionList(): array
    {
        if (null === $this->solutionList) {
            $this->solutionList = [];
            $em = $this->getEntityManager();
            $queryBuilder = $em->createQueryBuilder()
                ->select('s')
                ->from(Solution::class, 's');
            $queryBuilder->where('s.communeType = :communeType');
            $queryBuilder->setParameter('communeType', Solution::COMMUNE_TYPE_ALL);
            $results = $queryBuilder->getQuery()->execute();
            foreach ($results as $entity) {
                /** @var Solution $entity */
                $this->solutionList[$entity->getId()] = $entity;
            }
        }
        return $this->solutionList;
    }
}