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

use App\Entity\Base\NamedEntityInterface;
use App\Search\PhoneticUtility;
use App\Search\TextProcessor;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

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
            $recordIdPoints = [];
            $queryBuilder = $this->getQueryBuilderForEntityAndContext($entityClass, $context);
            $this->addQueryBuilderSearchConditions($queryBuilder, $searchParameters);
            $result = $queryBuilder->getQuery()->getArrayResult();
            $this->addResultRecordPoints($recordIdPoints, $result, $searchParameters);
            if (is_a($entityClass, NamedEntityInterface::class, true)) {
                $queryBuilder = $this->getQueryBuilderForEntityProperty($entityClass, 'name', $searchParameters);
                $result = $queryBuilder->getQuery()->getArrayResult();
                $this->addResultRecordPoints($recordIdPoints, $result, $searchParameters, 2000);
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
     * @param string $property
     * @param array $searchParameters
     * @return QueryBuilder
     */
    private function getQueryBuilderForEntityProperty(string $entityClass, string $property, array $searchParameters): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('si')
            ->from($entityClass, 'si');
        $queryBuilder = $queryBuilder->select('si.' . $property . ' AS baseword', 'si.id AS recordId');
        $searchConditions = [];
        foreach ($searchParameters as $word) {
            $searchConditions[] = $queryBuilder->expr()->like(
                'si.' . $property,
                $queryBuilder->expr()->literal('%' . $word . '%')
            );
        }
        $expr = $queryBuilder->expr()->orX()->addMultiple($searchConditions);
        $queryBuilder->andWhere($expr);
        return $queryBuilder;
    }

    private function getQueryBuilderForEntityAndContext(string $entityClass, string $context): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('si');
        $queryBuilder = $queryBuilder->select('si.baseword', 'si.recordId', 'SUM(si.occurrence) as wordCount')
            ->where('si.module = :recordType')
            ->andWhere('si.context = :context')
            ->groupBy('si.recordId')
            ->addGroupBy('si.baseword')
            ->setParameters([
                'recordType' => $entityClass,
                'context' => $context,
            ]);
        return $queryBuilder;
    }

    /**
     * Add the search conditions for the given search parameters to the query
     * @param QueryBuilder $queryBuilder
     * @param array $searchParameters
     * @return QueryBuilder
     */
    private function addQueryBuilderSearchConditions(QueryBuilder $queryBuilder, array $searchParameters): QueryBuilder
    {
        $searchConditions = [];
        foreach ($searchParameters as $word) {
            $wordLength = mb_strlen($word);
            if ($wordLength > 3 && !is_numeric($word)) {
                $mpStr = PhoneticUtility::getPhoneticRepresentationForWord($word);
            } else {
                $mpStr = '';
            }
            // If word has less than 3 characters, only match words that start with the given word
            $prefixCond = $wordLength > 3 ? '%' : '';
            // Compare numeric metaphone values if metaphone returns a non-empty string
            if ($mpStr !== '') {
                $searchConditions[] = $queryBuilder->expr()->andX(
                    'LENGTH(si.baseword) >= ' . $wordLength,
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq('si.metaphone', $queryBuilder->expr()->literal($mpStr)),
                        $queryBuilder->expr()->like('si.baseword', $queryBuilder->expr()->literal($prefixCond . $word . '%'))
                    )
                );
                //Numbers create empty metaphone string; compare these values with words directly
            } else {
                $searchConditions[] = $queryBuilder->expr()->like('si.baseword', $queryBuilder->expr()->literal($prefixCond . $word . '%'));
            }
        }
        $expr = $queryBuilder->expr()->orX()->addMultiple($searchConditions);
        $queryBuilder->andWhere($expr);
        return $queryBuilder;
    }

    /**
     * Set the result rating for the given result set
     * @param array $recordIdPoints
     * @param array $result
     * @param array $searchParameters
     * @param int $wordPointFactor
     */
    private function addResultRecordPoints(
        array &$recordIdPoints,
        array $result,
        array $searchParameters,
        int $wordPointFactor = 1000
    ): void
    {
        foreach ($result as $row) {
            if (!isset($recordIdPoints[$row['recordId']])) {
                $recordIdPoints[$row['recordId']] = 0;
                //$explain[$row['recordId']] = [];
            }
            $baseWord = mb_strtolower((string)$row['baseword']);
            $hasMultipleWords = strpos($baseWord, ' ') !== false;
            $baseWordLength = mb_strlen($baseWord);
            $points = 0;
            foreach ($searchParameters as $word) {
                $searchWordLength = mb_strlen((string)$word);
                $wordOffset = mb_strpos($baseWord, (string)$word);
                if ($wordOffset !== false) {
                    $lengthRatio = $searchWordLength / $baseWordLength;
                    // Increase ratio if base word starts with word
                    if ($wordOffset === 0) {
                        $lengthRatio = min(1, $lengthRatio + 0.15);
                    // Increase ratio if base word has multiple words and any word starts with word
                    } elseif ($hasMultipleWords && mb_strpos($baseWord, ' ' . $word) !== false) {
                        $lengthRatio = min(1, $lengthRatio + 0.075);
                    }
                    $wordCount = (int)max($row['wordCount'] ?? 1, 1);
                    $wordCountRatio = 0.5 + (0.5 / $wordCount);
                    $rating = (int)($searchWordLength * $wordPointFactor * $lengthRatio * $wordCountRatio);
                    $points += max(0, $rating);
                    /*$explain[$row['recordId']][] = [
                        '$baseWord' => $baseWord,
                        'word' => $word,
                        'rating' => $rating,
                        'lengthRatio' => $lengthRatio,
                        'wordCountRatio' => $wordCountRatio,
                        'lengthRating' => $searchWordLength * 1000,
                        'wordCount' => $row['wordCount'],
                    ];*/
                }
            }
            $recordIdPoints[$row['recordId']] += $points;
        }
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
