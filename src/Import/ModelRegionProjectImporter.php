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

use App\Entity\ModelRegionBeneficiary;
use App\Entity\ModelRegionProject;
use App\Entity\Organisation;
use App\Entity\Solution;
use App\Entity\Status;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ModelRegionProjectImporter extends AbstractCsvImporter
{
    /**
     * @var array
     */
    private const FIELD_MAP = [
        'id' => ['field' => 'importId', 'entity' => ModelRegionProject::class, 'type' => 'int', 'required' => true, 'auto_increment' => true],
        'projekttitel' => ['field' => 'name', 'entity' => ModelRegionProject::class, 'type' => 'string', 'required' => true],
        'projektbeschreibung' => ['field' => 'description', 'entity' => ModelRegionProject::class, 'type' => 'string'],
        'beginn_durchfuehrungszeitraum' => ['field' => 'projectStartAt', 'entity' => ModelRegionProject::class, 'type' => 'date'],
        'ende_durchfuehrungszeitraum' => ['field' => 'projectEndAt', 'entity' => ModelRegionProject::class, 'type' => 'date'],
        'alleinstellungsmerkmal_innovation_' => ['field' => 'usp', 'entity' => ModelRegionProject::class, 'type' => 'string'],
        'nutzen_fuer_alle_nrw_kommunen_' => ['field' => 'communesBenefits', 'entity' => ModelRegionProject::class, 'type' => 'string'],
        'uebertragbare_bzw_lizenzfreie_loesung' => ['field' => 'transferableService', 'entity' => ModelRegionProject::class, 'type' => 'string'],
        'geplanter_zeitpunkt_der_verfuegbarkeit_uebertragung_jahr_monat' => ['field' => 'transferableStart', 'entity' => ModelRegionProject::class, 'type' => 'string'],
    ];

    protected function getFieldMap(): array
    {
        $fieldMap = self::FIELD_MAP;
        $objectManager = $this->getManagerRegistry()->getManager();
        $fieldMap['zuwendungsempfaenger'] = [
            'field' => 'organisations',
            'entity' => ModelRegionProject::class,
            'type' => 'callback',
            'callback' => static function (string $value) use ($objectManager): array {
                $checkValues = explode(',', $value);
                $organisations = [];
                $repository = $objectManager->getRepository(Organisation::class);
                foreach ($checkValues as $checkVal) {
                    if ('' !== $dbVal = trim(str_replace('Stadt', '', $checkVal))) {
                        $organisation = $repository->findOneBy(['name' => $dbVal]);
                        if (null === $organisation) {
                            $object = new ModelRegionBeneficiary();
                            $organisation = $object->getOrganisation();
                            $organisation->setName(trim($checkVal));
                            $organisation->setFromReference($object);
                            $objectManager->persist($object);
                            if (!$objectManager->contains($organisation)) {
                                $objectManager->persist($organisation);
                            }
                        } elseif (empty($organisation->getName())) {
                            $organisation->setName(trim($checkVal));
                        }
                        $organisations[$organisation->getId()] = $organisation;
                    }
                }
                return $organisations;
            }
        ];
        return $fieldMap;
    }

    protected function getImportSourceKey(): string
    {
        return 'model_region_project_importer';
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
            $importClassProperties = $importRow[ModelRegionProject::class];
            $importId = (int)$importClassProperties['importId'];
            $targetEntity = $this->findEntityByConditions(ModelRegionProject::class, [
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
                $targetEntity = new ModelRegionProject();
                $targetEntity->setImportSource($this->getImportSourceKey());
                if (!empty($importClassProperties['importId'])) {
                    $targetEntity->setImportId((int)$importClassProperties['importId']);
                }
                $em->persist($targetEntity);
            } else {
                /** @var Solution $targetEntity */
                $targetEntity->setHidden(false);
            }
            $this->debug('Saving model region project: ' . $importClassProperties['name'] . ' [' . ($targetEntity->getId() ?: 'NEW') . ']');
            $targetEntity->setName($importClassProperties['name']);
            foreach ($importClassProperties as $propertyPath => $value) {
                if ($accessor->isWritable($targetEntity, $propertyPath)) {
                    $accessor->setValue($targetEntity, $propertyPath, $value);
                }
            }
            if (null !== $projectEndAt = $targetEntity->getProjectEndAt()) {
                $projectEndAt->setTime(23, 59, 59);
            }
            ++$rowOffset;
            if ($rowOffset % 100 === 0) {
                $em->flush();
            }
        }
        $em->flush();
    }
}
