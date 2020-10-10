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

namespace App\Import;

use App\Entity\EFile;
use App\Entity\EFileStatus;
use App\Entity\EFileStorageType;
use App\Entity\StateGroup\ServiceProvider;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;
use App\Entity\Status;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EFileImporter extends AbstractCsvImporter
{
    /**
     * @var array
     */
    private const FIELD_MAP = [
        'id' => ['field' => 'importId', 'entity' => EFile::class, 'type' => 'int', 'required' => true, 'auto_increment' => true],
        'DMS-Anwendung' => ['field' => 'name', 'entity' => EFile::class, 'type' => 'string', 'required' => true],
        'Kurzbeschreibung' => ['field' => 'description', 'entity' => EFile::class, 'type' => 'string'],
        'Betreiber' => ['field' => 'serviceProvider', 'entity' => EFile::class, 'type' => 'string', 'targetEntity' => ServiceProvider::class],
        'Status eAkte' => ['field' => 'status', 'entity' => EFile::class, 'type' => 'string', 'targetEntity' => EFileStatus::class],
        'Softwarebasis' => ['field' => 'specializedProcedures', 'entity' => EFile::class, 'type' => 'csv', 'targetEntity' => SpecializedProcedure::class],
        'Speichertechnik' => ['field' => 'storageTypes', 'entity' => EFile::class, 'type' => 'csv', 'targetEntity' => EFileStorageType::class],
        'Informationen' => ['field' => 'notes', 'entity' => EFile::class, 'type' => 'string'],
        'Wirtschaftlichkeitsbetrachtung' => ['field' => 'hasEconomicViabilityAssessment', 'entity' => EFile::class, 'type' => 'boolean'],
        'Investitionssumme' => ['field' => 'sumInvestments', 'entity' => EFile::class, 'type' => 'decimal'],
        'Folgekosten' => ['field' => 'followUpCosts', 'entity' => EFile::class, 'type' => 'decimal'],
        'Einsparpotentiale' => ['field' => 'savingPotentialNotes', 'entity' => EFile::class, 'type' => 'string'],
        'Führendes System' => ['field' => 'leadingSystem', 'entity' => EFile::class, 'type' => 'string', 'targetEntity' => SpecializedProcedure::class],
        'Beteiligte Softwaremodule' => ['field' => 'softwareModules', 'entity' => EFile::class, 'type' => 'csv', 'targetEntity' => SpecializedProcedure::class],
    ];

    protected function getFieldMap(): array
    {
        $fieldMap = self::FIELD_MAP;
        return $fieldMap;
    }

    protected function getImportSourceKey(): string
    {
        return 'efile_importer';
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
        $rowOffset = 0;
        $accessor = PropertyAccess::createPropertyAccessor();
        /** @var Status $status */
        foreach ($rows as $importRow) {
            $importClassProperties = $importRow[EFile::class];
            $importId = (int)$importClassProperties['importId'];
            $targetEntity = $this->findEntityByConditions(EFile::class, [
                //$expressionBuilder->eq('LOWER(e.name)', ':name'),
                $expressionBuilder->eq('e.importSource', ':importSource'),
                $expressionBuilder->eq('e.importId', ':importId'),
            ], [
                    //'name' => $solutionProperties['name'],
                    'importSource' => $this->getImportSourceKey(),
                    'importId' => $importId,
                ]
            );
            if (null === $targetEntity) {
                $targetEntity = new EFile();
                $targetEntity->setImportSource($this->getImportSourceKey());
                if (!empty($importClassProperties['importId'])) {
                    $targetEntity->setImportId((int)$importClassProperties['importId']);
                }
                $em->persist($targetEntity);
            } else {
                /** @var Solution $targetEntity */
                $targetEntity->setHidden(false);
            }
            $this->debug('Saving efile entity: ' . $importClassProperties['name'] . ' [' . ($targetEntity->getId() ?: 'NEW') . ']');
            $targetEntity->setName($importClassProperties['name']);
            foreach ($importClassProperties as $propertyPath => $value) {
                if ($accessor->isWritable($targetEntity, $propertyPath)) {
                    $accessor->setValue($targetEntity, $propertyPath, $value);
                }
            }
            ++$rowOffset;
            if ($rowOffset % 100 === 0) {
                $em->flush();
            }
        }
        $em->flush();
    }
}
