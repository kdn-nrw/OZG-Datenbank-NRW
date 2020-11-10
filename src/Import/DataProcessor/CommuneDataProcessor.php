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

namespace App\Import\DataProcessor;

use App\Entity\Base\BaseEntity;
use App\Entity\ImportEntityInterface;
use App\Entity\Repository\CommuneRepository;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\CommuneType;
use App\Import\Model\AbstractImportModel;
use App\Import\Model\CommuneImportModel;
use Doctrine\Persistence\ObjectManager;

class CommuneDataProcessor extends AbstractDataProcessor
{
    /**
     * @param string $importModelClass
     */
    public function setImportModelClass(string $importModelClass): void
    {
        parent::setImportModelClass($importModelClass);
        $objectManager = $this->getEntityManager();
        $callback = static function (string $value) use ($objectManager): Commune {
            $refCommune = self::findCommuneWithMatchingNameAndType(
                $objectManager,
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
        };
        $this->addCallback('constituency', $callback);
    }

    /**
     * @inheritDoc
     */
    protected function findOrCreateImportedEntity(string $entityClass, AbstractImportModel $importModel): BaseEntity
    {
        /** @var CommuneImportModel $importModel */
        $importKeyData = $importModel->getImportKeyData();
        $name = $importKeyData['name'];
        $communeTypeId = $importKeyData['communeType'];
        $em = $this->getEntityManager();
        $targetEntity = self::findCommuneWithMatchingNameAndType($em, $name, $communeTypeId);
        if (null === $targetEntity) {
            /** @var ImportEntityInterface $targetEntity */
            $targetEntity = new $entityClass();
        }
        if ($targetEntity instanceof Commune) {
            $excludeTypes = [Commune::TYPE_CONSTITUENCY, Commune::TYPE_CITY_REGION, Commune::TYPE_INDEPENDENT_CITY];
            $communeType = $importModel->getCommuneType();
            $communeTypeId = null !== $communeType ? $communeType->getId() : null;
            if (in_array($communeTypeId, $excludeTypes, true)) {
                $importModel->setConstituency(null);
                $targetEntity->setConstituency(null);
            }
            $targetEntity->setName($importModel->getName());
        }
        return $targetEntity;
    }

    /**
     * Finds and returns the best matching commune for the given name and type
     *
     * @param ObjectManager $objectManager
     * @param string $name
     * @param int|int[]|null $communeTypes
     * @param bool $allowEmptyType
     * @return Commune|null
     */
    private static function findCommuneWithMatchingNameAndType(ObjectManager $objectManager, string $name, $communeTypes, $allowEmptyType = true): ?Commune
    {
        /** @var CommuneRepository $repository */
        $repository = $objectManager->getRepository(Commune::class);
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
            if (null === $entity || self::isBetterMatch($name, $allowedCommuneTypes, $entity, $row)) {
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
    private static function isBetterMatch(string $name, ?array $allowedCommuneTypes, Commune $entity, Commune $checkEntity): bool
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
