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

use App\Entity\Base\BaseEntity;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Organisation;
use App\Entity\OrganisationEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCsvImporter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    abstract protected function getFieldMap(): array;

    /**
     * @var \Doctrine\Persistence\ManagerRegistry|ManagerRegistry
     */
    private $registry;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }


    /**
     * Process content of the given CSV import rows
     *
     * @param array $rows The imported rows
     */
    abstract protected function processImportRows(array $rows): void;

    /**
     * @return \Doctrine\Persistence\ManagerRegistry|ManagerRegistry
     */
    protected function getManagerRegistry()
    {
        return $this->registry;
    }

    /**
     * Run file import
     *
     * @param string $directory Absolute path to directory with CSV file(s)
     */
    public function run(string $directory): void
    {
        $this->debug(sprintf('Starting import for directory %s', $directory));
        if (!is_dir($directory) || !is_readable($directory)) {
            $message = sprintf('Directory %s does not exist or is not readable!', $directory);
            $this->debug($message, OutputInterface::VERBOSITY_DEBUG);
            $this->logger->error($message, ['directory' => $directory]);
        } else {
            $pattern = rtrim($directory, '/') . '/*.{csv}';
            $files = glob($pattern, GLOB_BRACE);
            if (empty($files)) {
                $this->debug('Found no files to import');
            } else {
                $this->debug(sprintf('Found %s file(s) for import: %s', count($files), implode(', ', $files)));
                foreach ($files as $file) {
                    if (($handle = fopen($file, 'rb')) !== FALSE) {
                        $rows = $this->getRowsFromCsvData($handle);
                        fclose($handle);
                        $this->processImportRows($rows);
                    }
                }
            }
            $this->debug(sprintf('Finished import'));
        }
    }

    /**
     * Create debug message with given verbosity
     *
     * @param string $message The message
     * @param int $verbosity The verbosity controls which messages are displayed
     */
    protected function debug(string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        if (null !== $this->output) {
            $debug = date('Y-m-d H:i:s') . ': ' . $message;
            $this->output->writeln($debug, OutputInterface::OUTPUT_NORMAL | $verbosity);
        }
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Converts the given file data to an array with field value rows
     * @param resource $handle
     * @return array
     */
    private function getRowsFromCsvData($handle): array
    {
        $delimiter = null;
        $possibleDelimiters = ['|', ';', ','];
        $rows = [];
        $firstLine = fgets($handle, 2000);
        $enclosure = strpos($firstLine, '"') === false ? '' : '"';
        foreach ($possibleDelimiters as $checkDelimiter) {
            if (strpos($firstLine, $checkDelimiter) !== false) {
                $delimiter = $checkDelimiter;
                break;
            }
        }
        $parser = new DataParser();
        $headers = [];
        $tmpHeaders = str_getcsv($firstLine, $delimiter, $enclosure);

        foreach ($tmpHeaders as $offset => $header) {
            if (empty($header)) {
                $headers[] = 'offset_' . $offset;
            } else {
                $headers[] = $this->getCleanFieldName($parser, $header);
            }
        }
        $rowNr = 1;
        while (($data = fgetcsv($handle, 10000, $delimiter, $enclosure)) !== FALSE) {
            $row = $this->parseCsvLine($data, $headers);
            if (!empty($row)) {
                if (null !== $parsedRow = $this->fillInRowData($row, $rowNr)) {
                    $rows[] = $parsedRow;
                }
            }
            ++$rowNr;
        }
        return $rows;
    }

    private function getCleanFieldName(DataParser $parser, $name)
    {
        return strtolower(
            str_replace(['/', ' ', '-', '(', ')', '.', '___', '__'],
                '_',
                $parser->cleanStringValue($parser->formatString(trim($name))))
        );
    }

    /**
     * Fill in data for DB queries and updates/inserts.
     * PDO::prepare will escape parameters automatically later.
     *
     * @param array $row
     * @param int $rowNr
     *
     * @return array|null Parsed row data
     */
    protected function fillInRowData(array $row, int $rowNr): ?array
    {
        $entityPropertyData = [];
        $parser = new DataParser();
        $fieldMap = $this->getFieldMap();
        $tmpFields = array_keys($fieldMap);
        $mapImportFieldNames = [];
        foreach ($tmpFields as $sourceField) {
            $mapImportFieldNames[$sourceField] = $this->getCleanFieldName($parser, $sourceField);
        }
        foreach ($mapImportFieldNames as $sourceField => $importFieldName) {
            $fieldData = $fieldMap[$sourceField];
            $val = null;
            if (!empty($fieldData['required']) && (!isset($row[$importFieldName]) || (string)$row[$importFieldName] === '')) {
                if (array_key_exists('auto_increment', $fieldData)) {
                    $row[$importFieldName] = $rowNr;
                } else {
                    return null;
                }
            }
            $trgField = $fieldData['field'];
            if (array_key_exists($importFieldName, $row)) {
                if (array_key_exists('fixedValue', $fieldData)) {
                    $val = $fieldData['fixedValue'];
                } elseif ($fieldData['type'] === 'callback') {
                    $val = $parser->formatCallback($row[$importFieldName], $fieldData['callback']);
                } elseif (!empty($fieldData['targetEntity'])) {
                    $mapToProperty = $fieldData['mapToProperty'] ?? 'name';
                    $val = $this->findOrCreateTargetEntity($row[$importFieldName], $fieldData['targetEntity'], $mapToProperty, $fieldData['type']);
                } else {
                    $val = $row[$importFieldName];
                    if (is_array($val)) {
                        $val = current($val);
                    }
                    $ccKey = ucwords(str_replace('_', ' ', $fieldData['type']));
                    $formatFunction = 'format' . str_replace(' ', '', $ccKey);
                    if (method_exists($parser, $formatFunction)) {
                        $val = $parser->$formatFunction($val);
                    }
                }
            }
            $entityPropertyData[$fieldData['entity']][$trgField] = $val;
        }
        return $entityPropertyData;
    }

    /**
     * Finds or creates the entity of the given entity class that matches the given value
     *
     * @param mixed $value
     * @param string $entityClass
     * @param string $mapValueToProperty
     * @param string $dataType
     * @return ArrayCollection|BaseEntityInterface|null
     */
    protected function findOrCreateTargetEntity(
        $value,
        string $entityClass,
        $mapValueToProperty = 'name',
        $dataType = 'string'
    )
    {
        $compareValue = trim(strip_tags($value));
        if (empty($compareValue)) {
            return null;
        }
        $em = $this->getManagerRegistry()->getManager();
        /** @var EntityRepository $repository */
        $repository = $em->getRepository($entityClass);
        if ($dataType === 'csv') {
            $mapValues = explode(',', $compareValue);
            $collection = new ArrayCollection();
            foreach ($mapValues as $listValue) {
                $listEntity = $this->findOrCreateTargetEntity($listValue, $entityClass, $mapValueToProperty, 'string');
                if (null !== $listEntity) {
                    $collection->add($listEntity);
                }
            }
            return $collection;
        }
        $hasChanges = false;
        $entity = $repository->findOneBy([$mapValueToProperty => $compareValue]);
        if (null === $entity) {
            $entity = new $entityClass();
            $setter = 'set' . ucfirst($mapValueToProperty);
            $entity->$setter($compareValue);
            $em->persist($entity);
            $hasChanges = true;
        }
        if ($entity instanceof OrganisationEntityInterface) {
            $organisation = $entity->getOrganisation();
            if (null === $organisation) {
                $organisation = new Organisation();
                $entity->setOrganisation($organisation);
                $organisation->setName($compareValue);
                $hasChanges = true;
            } elseif (empty($organisation->getName())) {
                $organisation->setName($compareValue);
                $hasChanges = true;
            }
        }
        if ($hasChanges) {
            $em->flush();
        }
        return $entity;
    }

    /**
     * Either find an existing entity by the given field or create a new entity
     * @param string $entityClass
     * @param array $expressions
     * @param array $parameters
     * @return BaseEntity|null
     */
    protected function findEntityByConditions(string $entityClass, array $expressions, array $parameters = []): ?BaseEntity
    {
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository($entityClass);
        $qb = $repository->createQueryBuilder('e')
            ->orderBy('e.id', 'ASC');
        $andX = $qb->expr()->andX();
        foreach ($expressions as $expr) {
            $andX->add($expr);
        }
        $qb->where($andX);
        if (!empty($parameters)) {
            $qb->setParameters($parameters);
        }
        $qb->setMaxResults(1);
        /** @var BaseEntity|null $entity */
        try {
            $entity = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $entity = null;
        }
        return $entity;
    }

    /**
     * Map the given csv line to the header names defined in the first row of the csv file
     *
     * @param array $csvRow Raw csv data
     * @param array $headers CSV headers found in the first line of the csv file
     * @return array|null
     */
    private function parseCsvLine(array $csvRow, array $headers): ?array
    {
        $row = array();
        foreach ($headers as $offset => $header) {
            if (isset($csvRow[$offset])) {
                $row[$header] = trim($csvRow[$offset]);
            } else {
                $lineText = str_replace("\n", ' ', implode('|', $csvRow));
                $errMsg = sprintf('CSV row column count does not match header column count: %s', $lineText);
                $this->debug($errMsg, OutputInterface::VERBOSITY_VERBOSE);
                return null;
            }
        }
        return $row;
    }
}
