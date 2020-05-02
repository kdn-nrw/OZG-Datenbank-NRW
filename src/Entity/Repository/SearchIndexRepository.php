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

namespace App\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class SearchIndexRepository extends EntityRepository
{
    /**
     * Find records for given class and context that match the given search term
     * @param string $entityClass
     * @param string $context
     * @param string $searchTerm
     * @return array|int[]|null
     */
    public function findMatchingIndexRecords(string $entityClass, string $context, string $searchTerm): ?array
    {
        $matchingRecordIds = null;
        $searchParameters = [];
        $searchWords = explode(' ', trim(strip_tags(mb_strtolower($searchTerm))));
        foreach ($searchWords as $offset => $tmpWord) {
            if (!empty($tmpWord)) {
                $key = 'word' . $offset;
                $searchParameters[$key] = $tmpWord;
            }
        }
        if (!empty($searchParameters)) {
            $matchingRecordIds = [0];
            $queryBuilder = $this->createQueryBuilder('si');
            $query = $queryBuilder->select('si.recordId')
                ->where('si.module = :recordType')
                ->andWhere('si.context = :context')
                ->groupBy('si.recordId')
                ->setParameters([
                    'recordType' => $entityClass,
                    'context' => $context,
                ]);
            $orConditions = [];
            foreach ($searchParameters as $word) {
                $wordLength = mb_strlen($word);
                if ($wordLength > 3 && !is_numeric($word)) {
                    $mpStr = metaphone($word);
                } else {
                    $mpStr = '';
                }
                // Compare numeric metaphone values if metaphone returns a non empty string
                if ($mpStr !== '') {
                    $mp = hexdec(substr(md5($mpStr), 0, 7));
                    $orConditions[] = $queryBuilder->expr()->andX(
                        'LENGTH(si.baseword) >= ' . $wordLength,
                        $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq('si.metaphone', $queryBuilder->expr()->literal($mp)),
                            $queryBuilder->expr()->like('si.baseword', $queryBuilder->expr()->literal('%'.$word.'%'))
                        )
                    );
                //Numbers create empty metaphone string; compare these values with words directly
                } else {
                    $orConditions[] = $queryBuilder->expr()->like('si.baseword', $queryBuilder->expr()->literal('%'.$word.'%'));
                }
            }
            $expr = $query->expr()->orX()->addMultiple($orConditions);
            $queryBuilder->andWhere($expr);
            $result = $query->getQuery()->getArrayResult();
            foreach ($result as $row) {
                $matchingRecordIds[] = $row['recordId'];
            }
        }
        return $matchingRecordIds;
    }

    /**
     * @param string $entityClass
     * @param string $context
     * @param int $entityId
     * @return string|null Date time string or null
     */
    public function getRecordLastIndexTime(string $entityClass, string $context, int $entityId): ?string
    {
        $queryBuilder = $this->createQueryBuilder('si');
        $query = $queryBuilder->select('MAX(si.modifiedAt)')
            ->where('si.module = :recordType')
            ->andWhere('si.context = :context')
            ->andWhere('si.recordId = :recordId')
            ->setParameters(
                [
                    'recordType' => $entityClass,
                    'recordId' => $entityId,
                    'context' => $context,
                ]
            )
            ->setMaxResults(1)
            ->getQuery();
        try {
            $result = $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $result = null;
        }
        return null !== $result ? current($result) : null;
    }
}
