<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Import\DataProcessor;

use App\Entity\FormServer;
use App\Entity\FormServerSolution;
use App\Entity\Maturity;
use App\Entity\Service;
use App\Entity\ServiceSolution;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;
use App\Entity\Status;
use App\Import\Model\SolutionImportModel;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SolutionDataProcessor extends AbstractDataProcessor
{

    private const IMPORT_STATUS_ID = 6;

    /**
     * @var FormServer
     */
    private $formServer;

    /**
     * @var Maturity
     */
    private $maturity;

    /**
     * Initialize maturity entity by id
     *
     * @param int $id
     */
    public function setMaturityById(int $id): void
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $this->maturity = $em->getRepository(Maturity::class)->find($id);
    }

    /**
     * Initialize form server entity by id
     *
     * @param int $id
     */
    public function setFormServerById(int $id): void
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $this->formServer = $em->getRepository(FormServer::class)->find($id);
    }

    /**
     * Process content of the loaded import rows
     */
    public function processImportedRows(): void
    {
        $resultCollection = $this->getResultCollection();
        $this->setPropertyFieldMap($resultCollection, get_class($resultCollection));
        $this->setPropertyFieldMap($resultCollection, $this->getImportModelClass());
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $status = $em->getRepository(Status::class)->find(self::IMPORT_STATUS_ID);
        $formServer = $this->formServer;
        $maturity = $this->maturity;
        if (null === $status || null === $formServer || null === $maturity) {
            $message = 'The default import values are not valid: ';
            if (null === $status) {
                $message .= ' default status is null [' . self::IMPORT_STATUS_ID . '];';
            }
            if (null === $formServer || null === $maturity) {
                $message .= ' default form server is null;';
            }
            if (null === $maturity) {
                $message .= ' default maturity is null;';
            }
            $this->logger->error($message);
            return;
        }
        /** @var Status $status */
        $modelEntityPropertyMapping = $this->getModelEntityPropertyMapping();
        $accessor = PropertyAccess::createPropertyAccessor();
        $rowOffset = 0;
        foreach ($resultCollection as $importModel) {
            /** @var SolutionImportModel $importModel */
            $targetEntity = $this->findOrCreateImportedEntity(Solution::class, $importModel);
            /** @var Solution $targetEntity */
            $formServerSolution = null;
            if (!$em->contains($targetEntity)) {
                $targetEntity->setStatus($status);
                $em->persist($targetEntity);
            } else {
                $targetEntity->setHidden(false);
                $formServerSolutions = $targetEntity->getFormServerSolutions();
                foreach ($formServerSolutions as $entity) {
                    if ($entity->getFormServer() === $formServer) {
                        $formServerSolution = $entity;
                        break;
                    }
                }
            }
            $this->debug('Saving solution: ' . $importModel->getName() . ' [' . ($targetEntity->getId() ?: 'NEW') . ']');
            $targetEntity->setName($importModel->getName());
            $targetEntity->setMaturity($this->maturity);
            $this->addSpecializedProcedures($targetEntity, $importModel->getSpecializedProcedures());
            $this->addServiceSolutions($targetEntity, $importModel->getServiceSolutions());
            if (null === $formServerSolution) {
                $formServerSolution = new FormServerSolution();
                $formServerSolution->setFormServer($formServer);
                $formServerSolution->setSolution($targetEntity);
                $em->persist($formServerSolution);
                $targetEntity->addFormServerSolution($formServerSolution);
            }
            $fssPropertyMapping = $modelEntityPropertyMapping[FormServerSolution::class];
            foreach ($fssPropertyMapping as $entityProperty => $modelProperty) {
                if ($accessor->isWritable($formServerSolution, $entityProperty)) {
                    $value = $accessor->getValue($importModel, $modelProperty);
                    $accessor->setValue($formServerSolution, $entityProperty, $value);
                }
            }
            ++$rowOffset;
            if ($rowOffset % 100 === 0) {
                $em->flush();
            }
        }
        $em->flush();
    }

    private function addSpecializedProcedures(Solution $solution, array $importValues): void
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $expressionBuilder = $em->getExpressionBuilder();
        foreach ($importValues as $importValue) {
            $importEntity = $this->findEntityByConditions(SpecializedProcedure::class, [
                $expressionBuilder->eq('LOWER(e.name)', ':name')
            ], [
                    'name' => trim($importValue)
                ]
            );
            if (null !== $importEntity) {
                /** @var SpecializedProcedure $importEntity */
                $solution->addSpecializedProcedure($importEntity);
            }
        }
    }

    /**
     * Add service solution entities to solution
     *
     * @param Solution $solution
     * @param array $importValues
     * @throws \Doctrine\ORM\ORMException
     */
    private function addServiceSolutions(Solution $solution, array $importValues): void
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $expressionBuilder = $em->getExpressionBuilder();
        foreach ($importValues as $importValue) {
            if (stripos($importValue, 'keine') === 0) {
                $importValue = 'nicht im LeiKa';
            }
            $service = $this->findEntityByConditions(Service::class, [
                $expressionBuilder->eq('LOWER(e.serviceKey)', ':value')
            ], [
                    'value' => trim($importValue)
                ]
            );
            if (null !== $service) {
                /** @var Service $service */
                $serviceSolution = null;
                $serviceSolutions = $solution->getServiceSolutions();
                foreach ($serviceSolutions as $entity) {
                    if ($entity->getService() === $service) {
                        $serviceSolution = $entity;
                        break;
                    }
                }
                if (null === $serviceSolution) {
                    $serviceSolution = new ServiceSolution();
                    $serviceSolution->setService($service);
                    $serviceSolution->setSolution($solution);
                    $serviceSolution->setMaturity($this->maturity);
                    $em->persist($serviceSolution);
                    $solution->addServiceSolution($serviceSolution);
                } else {
                    $serviceSolution->setMaturity($this->maturity);
                }
            }
        }
    }
}
