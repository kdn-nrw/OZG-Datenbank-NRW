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

use App\Entity\OrganisationEntityInterface;
use App\Entity\Repository\CommuneRepository;
use App\Entity\StateGroup\AdministrativeDistrict;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\CommuneType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CommuneImporter extends AbstractCsvImporter
{
    protected function getFieldMap(): array
    {
        $objectManager = $this->getManagerRegistry()->getManager();
        return [
            //'id' => ['field' => 'importId', 'entity' => Commune::class, 'type' => 'int', 'required' => true, 'auto_increment' => true],
            'Kommune/Kreis' => ['field' => 'name', 'entity' => Commune::class, 'type' => 'string', 'required' => true],
            'Kategorie' => ['field' => 'communeType', 'entity' => Commune::class, 'type' => 'string', 'targetEntity' => CommuneType::class],
            'Zugehörigkeit Kreis' => [
                'field' => 'constituency',
                'entity' => Commune::class,
                'type' => 'callback',
                'callback' => function (string $value) use ($objectManager): Commune {
                    $refCommune = $this->findCommuneWithMatchingNameAndType(
                        $value,
                        [Commune::TYPE_CONSTITUENCY, Commune::TYPE_CITY_REGION],
                        false
                    );
                    if (null === $refCommune) {
                        $communeType = $objectManager->find(CommuneType::class, Commune::TYPE_CONSTITUENCY);
                        $refCommune = new Commune();
                        $refCommune->setName(trim($value));
                        $refCommune->setCommuneType($communeType);
                        $objectManager->persist($refCommune);
                    } elseif (empty($refCommune->getName())) {
                        $refCommune->setName(trim($value));
                    }
                    return $refCommune;
                }
            ],
            'PLZ' => ['field' => 'zipCode', 'entity' => Commune::class, 'type' => 'string'],
            'Ort' => ['field' => 'town', 'entity' => Commune::class, 'type' => 'string'],
            'Strasse' => ['field' => 'street', 'entity' => Commune::class, 'type' => 'string'],
            'Internet' => ['field' => 'url', 'entity' => Commune::class, 'type' => 'string'],
            'E-Mail' => ['field' => 'mainEmail', 'entity' => Commune::class, 'type' => 'string'],
            'AGS' => ['field' => 'officialCommunityKey', 'entity' => Commune::class, 'type' => 'string'],
            'Reg-Bez' => ['field' => 'administrativeDistrict', 'entity' => Commune::class, 'type' => 'string', 'targetEntity' => AdministrativeDistrict::class],
        ];
    }

    protected function getImportSourceKey(): string
    {
        return 'commune_importer';
    }

    /**
     * Process content of the given CSV import rows
     *
     * @param array $rows The imported rows
     * @throws \Doctrine\ORM\Query\QueryException
     */
    protected function processImportRows(array $rows): void
    {
        /** @var EntityManager $em */
        $em = $this->getManagerRegistry()->getManager();
        $rowOffset = 0;
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($rows as $importRow) {
            $importClassProperties = $importRow[Commune::class];
            $communeType = $importClassProperties['communeType'];
            $communeTypeId = null !== $communeType ? $communeType->getId() : null;
            $targetEntity = $this->findCommuneWithMatchingNameAndType($importClassProperties['name'], $communeTypeId);
            if (null === $targetEntity) {
                $this->debug('No entity found for name: ' . $importClassProperties['name'] . ' [' . $importClassProperties['zipCode'] . ']' . ' [' . $communeTypeId . ']');
                $targetEntity = new Commune();
                $em->persist($targetEntity);
            } else {
                /** @var Commune $targetEntity */
                $this->debug('Found entity: ' . $importClassProperties['name'] . ' <=> ' . $targetEntity->getName() . ' [' . $targetEntity->getId() . ']');
                $targetEntity->setHidden(false);
            }
            $excludeTypes = [Commune::TYPE_CONSTITUENCY, Commune::TYPE_CITY_REGION, Commune::TYPE_INDEPENDENT_CITY];
            if (in_array($communeTypeId, $excludeTypes, true)) {
                $importClassProperties['constituency'] = null;
                $targetEntity->setConstituency(null);
            }
            $targetEntity->setName($importClassProperties['name']);
            foreach ($importClassProperties as $propertyPath => $value) {
                if ($accessor->isWritable($targetEntity, $propertyPath)) {
                    $accessor->setValue($targetEntity, $propertyPath, $value);
                }
            }
            if ($targetEntity instanceof OrganisationEntityInterface) {
                $organisation = $targetEntity->getOrganisation();
                foreach ($importClassProperties as $propertyPath => $value) {
                    if ($accessor->isWritable($organisation, $propertyPath)) {
                        $accessor->setValue($organisation, $propertyPath, $value);
                    }
                }
                if (!$em->contains($organisation)) {
                    $em->persist($organisation);
                }
            }
            ++$rowOffset;
            if ($rowOffset % 100 === 0) {
                $em->flush();
            }
        }
        $em->flush();
    }

    /**
     * Finds and returns the best matching commune for the given name and type
     *
     * @param string $name
     * @param int|int[]|null $communeTypes
     * @param bool $allowEmptyType
     * @return Commune|null
     */
    private function findCommuneWithMatchingNameAndType(string $name, $communeTypes, $allowEmptyType = true): ?Commune
    {
        /** @var CommuneRepository $repository */
        $repository = $this->getManagerRegistry()->getRepository(Commune::class);
        $qb = $repository->createQueryBuilder('e')
            ->orderBy('e.id', 'ASC');
        $checkDbVal = str_replace('-', ' ', mb_strtolower(trim($name)));
        if (null !== $communeTypes) {
            $allowedCommuneTypes = is_array($communeTypes) ? array_filter($communeTypes) : [(int)$communeTypes];
        } else {
            $allowedCommuneTypes = [];
        }
        if (strpos($name, 'Städteregion') !== false) {
            $qb->where(
                'REPLACE(LOWER(e.name), \'-\', \' \') LIKE :likeName'
            );
            $qb->setParameters([
                'likeName' => '%' . $checkDbVal . '%',
            ]);
        } else {
            $qb->where(
                $qb->expr()->orX(
                    ':checkDbVal LIKE CONCAT(\'%\', REPLACE(LOWER(e.name), \'-\', \' \'), \'%\')',
                    'REPLACE(LOWER(e.name), \'-\', \' \') LIKE :likeName'
                )
            );
            $qb->setParameters([
                'likeName' => '%' . $checkDbVal . '%',
                'checkDbVal' => $checkDbVal,
            ]);
        }

        if (!empty($allowedCommuneTypes)) {
            if ($allowEmptyType) {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->isNull('e.communeType'),
                        $qb->expr()->in('e.communeType', $allowedCommuneTypes)
                    )
                );
            } else {
                $qb->andWhere(
                    $qb->expr()->in('e.communeType', $allowedCommuneTypes)
                );
            }
        }
        $result = $qb->getQuery()->getResult();
        $entity = null;
        foreach ($result as $row) {
            /** @var Commune $result */
            if (null === $entity || $this->isBetterMatch($name, $allowedCommuneTypes, $entity, $row)) {
                $entity = $row;
            }
        }
        /** @var Commune|null $entity */
        return $entity;
    }

    /**
     * Returns true if the name if the check entity is a better match to the given name than the currently entity used
     *
     * @param string $name
     * @param array|null $allowedCommuneTypes
     * @param Commune $entity
     * @param Commune $checkEntity
     * @return bool
     */
    private function isBetterMatch(string $name, ?array $allowedCommuneTypes, Commune $entity, Commune $checkEntity): bool
    {
        $isBetterMatch = false;
        $oldName = $entity->getName();
        $checkName = $checkEntity->getName();
        if ($checkName !== $name && $oldName !== $name) {
            $lenA = mb_strlen($name);
            $lenB = mb_strlen($oldName);
            $lenC = mb_strlen($checkName);
            // Return true if the name is shorted
            if ($lenC > $lenA) {
                $isBetterMatch = $lenC < $lenB;
            } else {
                $isBetterMatch = ($lenA - $lenC) < ($lenA - $lenB);
            }
        }
        // If the name is not a better match, check if the commune type is a better match
        if (!$isBetterMatch && !empty($allowedCommuneTypes)) {
            $oldCommuneType = $entity->getCommuneType();
            $checkCommuneType = $checkEntity->getCommuneType();
            $isBetterMatch = null !== $checkCommuneType && in_array($checkCommuneType->getId(), $allowedCommuneTypes, false)
                && (null === $oldCommuneType || !in_array($oldCommuneType->getId(), $allowedCommuneTypes, false));
        }
        return $isBetterMatch;
    }
}
