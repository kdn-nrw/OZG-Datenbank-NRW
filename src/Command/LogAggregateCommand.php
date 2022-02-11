<?php

declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Statistics\LogPathInfo;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("13 3 * * 6")
 */
class LogAggregateCommand extends Command
{
    use InjectManagerRegistryTrait;

    protected static $defaultName = 'app:log:aggregate';

    /**
     * @var array
     */
    private $mapPaths;

    /**
     * @var array
     */
    private $mapLogEntries;

    /**
     * @var array
     */
    private $mapSearchEntries;

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Aggregate log entries');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $startTime = microtime(true);
        //$this->executeStatement('TRUNCATE TABLE ozg_statistics_log_summary');
        //$this->executeStatement('TRUNCATE TABLE ozg_statistics_log_search');
        $minTime = new \DateTime();
        $minTime->modify('first day of this week midnight');
        $minTime->setTimezone(new \DateTimeZone('UTC'));
        $dateString = $minTime->format('Y-m-d H:i:s');
        $query = "SELECT COUNT(*) AS total_count FROM ozg_statistics_log_entry WHERE created_at < '$dateString'";
        $totalRowCount = $this->fetchOne($query);
        $importedRowCount = 0;
        if ($totalRowCount > 0) {
            $this->initPathMap();
            $query = 'SELECT created_at FROM ozg_statistics_log_entry ORDER BY id ASC LIMIT 1';
            $checkDateStr = $this->fetchOne($query);
            if ($checkDateStr) {
                $minDate = date_create($checkDateStr, new \DateTimeZone('UTC'));
                $minDate->setTimezone(new \DateTimeZone('Europe/Berlin'));
                $dateString = $minDate->format('Y-m-d');
                $this->initLogEntriesMap($dateString);
            } else {
                $this->initLogEntriesMap();
            }
            $this->initSearchEntriesMap();
            $query = "SELECT COUNT(*) AS total_count FROM ozg_statistics_log_entry WHERE created_at < '$dateString'";
            $totalRowCount = $this->fetchOne($query);
            $maxRowCount = min($totalRowCount, 600000);
            $rowsPerRequest = 5000;
            $timeLimitInSeconds = 600;
            $offset = 0;
            $deleteIdStatements = [];
            do {
                $query = "SELECT id, created_at, path_info, route, query_parameters FROM ozg_statistics_log_entry WHERE created_at < '$dateString' LIMIT $rowsPerRequest OFFSET $offset";
                $rows = $this->fetchAllAssociative($query);
                if (!empty($rows)) {
                    $this->processLogRows($rows);
                    $idList = array_column($rows, 'id');
                    $deleteIdStatements[] = 'DELETE FROM ozg_statistics_log_entry WHERE id IN ('.implode(', ', $idList).')';
                    $offset += count($rows);
                }
                $durationSeconds = round(microtime(true) - $startTime, 3);

            } while ($durationSeconds < $timeLimitInSeconds && !empty($rows) && $offset < $maxRowCount);
            $importedRowCount = $offset;
            if (!empty($this->mapLogEntries)) {
                $fieldTypes = [
                    'entry_date' => '%s',
                    'access_count' => '%d',
                    'path_info_id' => '%d',
                ];
                $this->saveMultipleItems('ozg_statistics_log_summary', $this->mapLogEntries, $fieldTypes);
            }
            if (!empty($this->mapSearchEntries)) {
                $fieldTypes = [
                    'search_term' => '%s',
                    'search_count' => '%d',
                    'path_info_id' => '%d',
                ];
                $this->saveMultipleItems('ozg_statistics_log_search', $this->mapSearchEntries, $fieldTypes);
            }
            foreach ($deleteIdStatements as $sql) {
                $this->executeStatement($sql);
            }
        }
        // ozg_statistics_log_search
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished log aggregation. %d records of %d were aggregated in %s seconds', $importedRowCount, $totalRowCount, $durationSeconds));
    }

    private function initPathMap()
    {
        $this->mapPaths = [];
        $query = 'SELECT id, route FROM ozg_statistics_log_path_info ORDER BY id ASC';
        $rows = $this->fetchAllAssociative($query);
        foreach ($rows as $row) {
            $route = (string) $row['route'];
            $entityId = (int) $row['id'];
            /*$path = (string) $row['path'];
            $isFrontend = (string) $row['is_frontend'];*/
            $this->mapPaths[$route] = $entityId;
        }
    }

    private function initLogEntriesMap($dateString = null)
    {
        $this->mapLogEntries = [];
        $query = 'SELECT * FROM ozg_statistics_log_summary';
        if (!empty($dateString)) {
            $query .= " WHERE entry_date >= '$dateString'";
        }
        $query .= ' ORDER BY id ASC';
        $rows = $this->fetchAllAssociative($query);
        foreach ($rows as $row) {
            $key = $row['entry_date'] . '_' . $row['path_info_id'];
            $this->mapLogEntries[$key] = [
                'id' => (int) $row['id'],
                'entry_date' => $row['entry_date'],
                'access_count' => $row['access_count'],
                'path_info_id' => $row['path_info_id'],
                '_hasChanges' => false,
            ];
        }
    }

    private function initSearchEntriesMap()
    {
        $this->mapSearchEntries = [];
        $query = 'SELECT * FROM ozg_statistics_log_search ORDER BY id ASC';
        $rows = $this->fetchAllAssociative($query);
        foreach ($rows as $row) {
            $searchValue = $row['search_term'];
            $searchKey = str_replace(['-', ' ', '.', ';'], '_', $searchValue) . '_' . $row['path_info_id'];
            $this->mapSearchEntries[$searchKey] = [
                'id' => (int) $row['id'],
                'search_term' => $row['search_term'],
                'search_count' => $row['search_count'],
                'path_info_id' => $row['path_info_id'],
                '_hasChanges' => false,
            ];
        }
    }

    private function getPathInfoId(string $path, string $route): int
    {
        if (!isset($this->mapPaths[$route])) {
            $this->mapPaths[$route] = $this->createPathInfo($path, $route);
        }
        return $this->mapPaths[$route];
    }

    private function processLogRows($rows) {

        foreach ($rows as $row) {
            $route = (string) $row['route'];
            if (empty($route)) {
                continue;
            }
            //$entityId = (int) $row['id'];
            $dateTime = new \DateTime($row['created_at'], new \DateTimeZone('UTC'));
            $dateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
            $date = $dateTime->format('Y-m-d');
            $pathInfoId = $this->getPathInfoId($row['path_info'], $route);
            $key = $date . '_' . $pathInfoId;
            if (!isset($this->mapLogEntries[$key])) {
                $this->mapLogEntries[$key] = [
                    'id' => null,
                    'entry_date' => $date,
                    'access_count' => 1,
                    'path_info_id' => $pathInfoId,
                ];
            } else {
                $this->mapLogEntries[$key]['access_count'] += 1;
                $this->mapLogEntries[$key]['_hasChanges'] = true;
            }
            if (strpos($row['query_parameters'], 'filter') !== false) {
                $queryParameters = json_decode($row['query_parameters'], true);
                $searchTerm = null;
                if (isset($queryParameters['filter'])) {
                    $filterValues = $queryParameters['filter'];
                    if (!empty($filterValues['fullText']['value'])) {
                        $searchTerm = $filterValues['fullText']['value'];
                    } elseif (!empty($filterValues['name']['value'])) {
                        $searchTerm = $filterValues['name']['value'];
                    }
                    if ($searchTerm && strlen($searchTerm) > 2
                        && strpos($searchTerm, '<script>') === false
                        && strpos($searchTerm, 'union all') === false) {
                        $saveSearchTerm = $this->cleanSqlValue(mb_strtolower($searchTerm));
                        if (mb_strlen($saveSearchTerm) > 200) {
                            $saveSearchTerm = mb_substr($saveSearchTerm, 0, 200);
                        }
                        $searchKey = str_replace(['-', ' ', '.', ';'], '_', $saveSearchTerm) . '_' . $pathInfoId;
                        if (!isset($this->mapSearchEntries[$searchKey])) {
                            $this->mapSearchEntries[$searchKey] = [
                                'id' => null,
                                'search_term' => $saveSearchTerm,
                                'search_count' => 1,
                                'path_info_id' => $pathInfoId,
                            ];
                        } else {
                            $this->mapSearchEntries[$searchKey]['search_count'] += 1;
                            $this->mapSearchEntries[$searchKey]['_hasChanges'] = true;
                        }
                    }
                }
            }
        }
    }

    private function cleanSqlValue($value)
    {
        return str_replace(["'", '\\'], '', $value);
    }

    /**
     * @param string $path
     * @param string $route
     * @return null|int
     * @throws \Doctrine\DBAL\Exception
     */
    protected function createPathInfo(string $path, string $route)
    {
        if (strpos($route, 'frontend') === 0) {
            $pathType = LogPathInfo::PATH_TYPE_FRONTEND;
        } elseif (strpos($route, 'api') === 0) {
            $pathType = LogPathInfo::PATH_TYPE_API;
        } else {
            $pathType = LogPathInfo::PATH_TYPE_BACKEND;
        }
        $dateCreated = date_create();
        $dateCreated->setTimezone(new \DateTimeZone('UTC'));
        $dateStr = $dateCreated->format('Y-m-d H:i:s');
        $savePath = $this->cleanSqlValue(strtolower($path));
        $values = [
            $savePath, $route, $pathType, $dateStr, $dateStr
        ];
        $sql = 'INSERT INTO ozg_statistics_log_path_info (path, route, path_type, modified_at, created_at) VALUES ' . "('" . implode("', '", $values) . "')";
        $this->executeStatement($sql);
        $connection = $this->getEntityManager()->getConnection();
        $lastInsertId = $connection->lastInsertId();
        if ($lastInsertId) {
            return (int) $lastInsertId;
        }
        return null;
    }

    /**
     * Save the given data in the table
     *
     * @param string $tableName
     * @param array $recordList
     * @param array $fieldTypes
     * @throws \Doctrine\DBAL\Exception
     */
    private function saveMultipleItems(string $tableName, array $recordList, array $fieldTypes): void
    {
        if (!empty($recordList)) {
            $connection = $this->getEntityManager()->getConnection();
            $fieldTypes['modified_at'] = '%s';
            $fieldTypesNew = $fieldTypes;
            $fieldTypesNew['created_at'] = '%s';
            $insert = 'INSERT INTO '.$tableName.' ('.implode(', ', array_keys($fieldTypesNew)).') VALUES ';
            $sqlStatements = [];
            $insertValueList = [];
            $count = 1;
            $dateCreated = date_create();
            $dateCreated->setTimezone(new \DateTimeZone('UTC'));
            $dateStr = $dateCreated->format('Y-m-d H:i:s');
            $placeholder = '('.implode(', ', $fieldTypesNew).')';
            foreach ($recordList as $values) {
                $values['modified_at'] = $dateStr;
                $recordId = $values['id'];
                unset($values['id']);
                if (empty($recordId)) {
                    unset($values['_hasChanges']);
                    $values['created_at'] = $dateStr;
                    $bind = [];
                    foreach ($fieldTypesNew as $field => $fieldFormat) {
                        $bind[] = $fieldFormat === '%d' ?  (int) $values[$field] : $connection->quote($values[$field]);
                    }
                    $insertValueList[] = vsprintf($placeholder, $bind);
                    if ($count > 100) {
                        $sqlStatements[] = $insert . implode(', ', $insertValueList);
                        $insertValueList = [];
                        $count = 0;
                    }
                    ++$count;
                } elseif ($values['_hasChanges']) {
                    $sql = 'UPDATE '.$tableName.' SET ';
                    $bind = [];
                    foreach ($fieldTypes as $field => $fieldFormat) {
                        $quotedValue = $fieldFormat === '%d' ?  (int) $values[$field] : $connection->quote($values[$field]);
                        $bind[$field] = sprintf($field . ' = ' . $fieldFormat, $quotedValue);
                    }
                    $sql .= implode(', ', $bind) . ' WHERE id = ' . (int) $recordId;
                    $sqlStatements[] = $sql;
                }
            }
            if (!empty($insertValueList)) {
                $sqlStatements[] = $insert . implode(', ', $insertValueList);
            }
            foreach ($sqlStatements as $sql) {
                $this->executeStatement($sql);
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

    /**
     * Execute a raw sql statement; used instead of Doctrine DQL for performance reasons
     *
     * @param string $sql
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fetchAllAssociative(string $sql)
    {
        $connection = $this->getEntityManager()->getConnection();
        if (method_exists($connection, 'fetchAllAssociative')) {
            return $connection->fetchAllAssociative($sql);
        }
        return $connection->fetchAll($sql);
    }

    /**
     * Execute a raw sql statement; used instead of Doctrine DQL for performance reasons
     *
     * @param string $sql
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fetchOne(string $sql)
    {
        $connection = $this->getEntityManager()->getConnection();
        if (method_exists($connection, 'fetchAllAssociative')) {
            return $connection->fetchOne($sql);
        }
        return $connection->fetchColumn($sql);
    }
}