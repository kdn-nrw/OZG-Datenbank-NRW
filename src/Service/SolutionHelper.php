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

namespace App\Service;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Solution;
use App\Entity\StateGroup\Commune;

class SolutionHelper
{
    use InjectManagerRegistryTrait;

    /**
     * Persist manually added commune solutions
     * @param Solution $object
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateCommuneReferences(Solution $object): void
    {
        $communeType = $object->getCommuneType();
        if ($communeType === Solution::COMMUNE_TYPE_SELECTED) {
            $communes = $object->getCommunes();
        } else {
            $em = $this->getEntityManager();
            $communes = $em->getRepository(Commune::class)->findAll();
        }
        $this->updateCommuneSolutions($object, $communes, $communeType);
    }

    /**
     * Persist manually added commune solutions
     *
     * @param Solution $object
     * @param iterable $communes
     * @param string $communeType
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateCommuneSolutions(Solution $object, iterable $communes, string $communeType): void
    {
        $communeSolutions = $object->getCommuneSolutions();
        $mappedCommunes = [];
        foreach ($communeSolutions as $communeSolution) {
            if (null !== $commune = $communeSolution->getCommune()) {
                $mappedCommunes[$commune->getId()] = $communeSolution;
            }
        }
        $selectedCommunes = [];
        $insertCommunes = [];
        foreach ($communes as $commune) {
            $selectedCommunes[] = $commune->getId();
            if (!array_key_exists($commune->getId(), $mappedCommunes)) {
                $insertCommunes[] = $commune->getId();
            }
        }
        $solutionId = $object->getId();
        if (!empty($insertCommunes)) {
            $insSql = 'INSERT'.' INTO ozg_solutions_communes (solution_id, commune_id, commune_type, connection_planned, modified_at, created_at, hidden)';
            $valueRows = [];
            foreach ($insertCommunes as $communeId) {
                $valueRows[] = "($solutionId, $communeId, '$communeType', 0, NOW(), NOW(), 0)";
            }
            $insSql .= ' VALUES ' . implode(', ', $valueRows);
            $this->executeStatement($insSql);
        }
        // Use SQL statements because entity manager is too slow!
        $sql = "UPDATE ozg_solutions_communes SET commune_type = '$communeType' WHERE solution_id = $solutionId";
        $this->executeStatement($sql);
        if ($communeType === Solution::COMMUNE_TYPE_SELECTED) {
            $deleteItems = [];
            foreach ($mappedCommunes as $communeId => $communeSolution) {
                if (!in_array($communeId, $selectedCommunes, true)) {
                    $deleteItems[] = $communeSolution->getId();
                }
            }
            if (!empty($deleteItems)) {
                $delSql = 'DELETE FROM ozg_solutions_communes WHERE id IN (' . implode(', ', $deleteItems) . ')';
                $this->executeStatement($delSql);
            }
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

}