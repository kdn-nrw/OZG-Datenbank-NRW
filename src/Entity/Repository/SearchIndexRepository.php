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

use App\Search\PhoneticUtility;
use App\Search\TextProcessor;
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
        $fullTextSearchWords = TextProcessor::createWordListForText($searchTerm);
        $searchWords = array_keys($fullTextSearchWords);
        foreach ($searchWords as $offset => $tmpWord) {
            if (!empty($tmpWord)) {
                $key = 'word' . $offset;
                $searchParameters[$key] = $tmpWord;
            }
        }
        if (!empty($searchParameters)) {
            $queryBuilder = $this->createQueryBuilder('si');
            $query = $queryBuilder->select('si.baseword', 'si.recordId')
                ->where('si.module = :recordType')
                ->andWhere('si.context = :context')
                ->groupBy('si.recordId')
                ->addGroupBy('si.baseword')
                ->setParameters([
                    'recordType' => $entityClass,
                    'context' => $context,
                ]);
            $searchConditions = [];
            foreach ($searchParameters as $word) {
                $wordLength = mb_strlen($word);
                if ($wordLength > 3 && !is_numeric($word)) {
                    $mpStr = PhoneticUtility::getPhoneticRepresentationForWord($word);
                } else {
                    $mpStr = '';
                }
                // Compare numeric metaphone values if metaphone returns a non empty string
                if ($mpStr !== '') {
                    $searchConditions[] = $queryBuilder->expr()->andX(
                        'LENGTH(si.baseword) >= ' . $wordLength,
                        $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq('si.metaphone', $queryBuilder->expr()->literal($mpStr)),
                            $queryBuilder->expr()->like('si.baseword', $queryBuilder->expr()->literal('%' . $word . '%'))
                        )
                    );
                    //Numbers create empty metaphone string; compare these values with words directly
                } else {
                    $searchConditions[] = $queryBuilder->expr()->like('si.baseword', $queryBuilder->expr()->literal('%' . $word . '%'));
                }
            }
            $expr = $query->expr()->orX()->addMultiple($searchConditions);
            $queryBuilder->andWhere($expr);
            $result = $query->getQuery()->getArrayResult();
            $recordIdPoints = [];
            foreach ($result as $row) {
                if (!isset($recordIdPoints[$row['recordId']])) {
                    $recordIdPoints[$row['recordId']] = 0;
                }
                $baseWord = (string) $row['baseword'];
                $baseWordLength = mb_strlen($baseWord);
                $points = 0;
                foreach ($searchParameters as $word) {
                    $searchWordLength = mb_strlen((string) $word);
                    $wordOffset = mb_strpos($baseWord, (string) $word);
                    if ($wordOffset !== false) {
                        $points += max(0, $searchWordLength * 1000 - $wordOffset - $baseWordLength);
                    }
                }
                $recordIdPoints[$row['recordId']] += $points;
            }
            // Only return significant results
            if (!empty($recordIdPoints)) {
                $matchingRecordIds = [];
                arsort($recordIdPoints);
                $offset = 0;
                foreach ($recordIdPoints as $recordId => $points) {
                    $matchingRecordIds[] = $recordId;
                    if ($offset > 50 && $points < $points * $offset) {
                        break;
                    }
                    ++$offset;
                }
            } else {
                $matchingRecordIds = [0];
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
