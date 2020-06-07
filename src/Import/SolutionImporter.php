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

namespace App\Import;

use App\Entity\FormServer;
use App\Entity\FormServerSolution;
use App\Entity\Maturity;
use App\Entity\Service;
use App\Entity\ServiceSolution;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;
use App\Entity\Status;
use Doctrine\ORM\EntityManager;

class SolutionImporter extends AbstractCsvImporter
{
    /**
     * @var array
     */
    private const FIELD_MAP = [
        'id' => array('field' => 'importId', 'entity' => Solution::class, 'type' => 'int', 'required' => true),
        'name' => array('field' => 'name', 'entity' => Solution::class, 'type' => 'string', 'required' => true),
        'fachverfahren' => array('field' => 'specializedProcedures', 'entity' => Solution::class, 'type' => 'csv'),
        'leika_id' => array('field' => 'serviceSolutions', 'entity' => Solution::class, 'type' => 'csv'),
        'artikelnummer' => array('field' => 'articleNumber', 'entity' => FormServerSolution::class, 'type' => 'string', 'required' => true),
        'assistententyp' => array('field' => 'assistantType', 'entity' => FormServerSolution::class, 'type' => 'string'),
        'identifikationsnummer' => array('field' => 'articleKey', 'entity' => FormServerSolution::class, 'type' => 'string'),
        'druckvorlage_geeignet' => array('field' => 'usableAsPrintTemplate', 'entity' => FormServerSolution::class, 'type' => 'boolean'),
    ];

    private const IMPORT_STATUS_ID = 6;

    protected function getFieldMap(): array
    {
        return self::FIELD_MAP;
    }

    protected function getImportSourceKey(): string
    {
        return 'solution_importer';
    }

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
        $em = $this->getManagerRegistry()->getManager();
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
        $em = $this->getManagerRegistry()->getManager();
        $this->formServer = $em->getRepository(FormServer::class)->find($id);
    }

    /**
     * Process content of the given CSV import rows
     *
     * @param array $rows The imported rows
     */
    protected function processImportRows(array $rows): void
    {
        /** @var EntityManager $em */
        $em = $this->getManagerRegistry()->getManager();
        $expressionBuilder = $em->getExpressionBuilder();
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
            $this->getLogger()->error($message);
            return;
        }
        $rowOffset = 0;
        /** @var Status $status */
        foreach ($rows as $importRow) {
            $importClassProperties = $importRow[Solution::class];
            $importId = (int)$importClassProperties['importId'];
            $targetEntity = $this->findEntityByConditions(Solution::class, [
                //$expressionBuilder->eq('LOWER(e.name)', ':name'),
                $expressionBuilder->eq('e.importSource', ':importSource'),
                $expressionBuilder->eq('e.importId', ':importId'),
            ], [
                    //'name' => $solutionProperties['name'],
                    'importSource' => $this->getImportSourceKey(),
                    'importId' => $importId,
                ]
            );
            $formServerSolution = null;
            if (null === $targetEntity) {
                $targetEntity = new Solution();
                $targetEntity->setStatus($status);
                $targetEntity->setImportSource($this->getImportSourceKey());
                if (!empty($importClassProperties['importId'])) {
                    $targetEntity->setImportId((int)$importClassProperties['importId']);
                }
                $em->persist($targetEntity);
            } else {
                /** @var Solution $targetEntity */
                $targetEntity->setHidden(false);
                $formServerSolutions = $targetEntity->getFormServerSolutions();
                foreach ($formServerSolutions as $entity) {
                    if ($entity->getFormServer() === $formServer) {
                        $formServerSolution = $entity;
                        break;
                    }
                }
            }
            $this->debug('Saving solution: ' . $importClassProperties['name'] . ' [' . ($targetEntity->getId() ?: 'NEW') . ']');
            $targetEntity->setName($importClassProperties['name']);
            $targetEntity->setMaturity($this->maturity);
            $this->addSpecializedProcedures($targetEntity, $importClassProperties['specializedProcedures']);
            $this->addServiceSolutions($targetEntity, $importClassProperties['serviceSolutions']);
            if (null === $formServerSolution) {
                $formServerSolution = new FormServerSolution();
                $formServerSolution->setFormServer($formServer);
                $formServerSolution->setSolution($targetEntity);
                $em->persist($formServerSolution);
                $targetEntity->addFormServerSolution($formServerSolution);
            }
            $properties = $importRow[FormServerSolution::class];
            if (!empty($properties['articleNumber'])) {
                $formServerSolution->setArticleNumber((string)$properties['articleNumber']);
            }
            if (!empty($properties['assistantType'])) {
                $formServerSolution->setAssistantType((string)$properties['assistantType']);
            }
            if (!empty($properties['articleKey'])) {
                $formServerSolution->setArticleKey((string)$properties['articleKey']);
            }
            if (array_key_exists('usableAsPrintTemplate', $properties)) {
                $formServerSolution->setUsableAsPrintTemplate((bool)$properties['usableAsPrintTemplate']);
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
        $em = $this->getManagerRegistry()->getManager();
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
        $em = $this->getManagerRegistry()->getManager();
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
