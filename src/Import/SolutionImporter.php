<?php

namespace App\Import;

use App\Entity\Base\BaseEntity;
use App\Entity\FormServer;
use App\Entity\FormServerSolution;
use App\Entity\Maturity;
use App\Entity\Service;
use App\Entity\ServiceSolution;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;
use App\Entity\Status;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SolutionImporter implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var array
     */
    private const FIELD_MAP = [
        'id' => array('field' => 'importId', 'entity' => Solution::class, 'type' => 'int', 'required' => true),
        'name' => array('field' => 'name', 'entity' => Solution::class, 'type' => 'string', 'required' => true),
        'fachverfahren' => array('field' => 'specializedProcedures', 'entity' => Solution::class, 'type' => 'csv'),
        'leika_id' => array('field' => 'serviceSolutions', 'entity' => Solution::class, 'type' => 'csv'),
        'artikelnummer' => array('field' => 'articleNumber', 'entity' => FormServerSolution::class, 'type' => 'string', 'required' => true),
        'assistententyp' => array('field' => 'assistantType', 'entity' => FormServerSolution::class, 'type' => 'string'),
        'identifikationsnummer' => array('field' => 'articleKey', 'entity' => FormServerSolution::class, 'type' => 'string'),
        'druckvorlage_geeignet' => array('field' => 'usableAsPrintTemplate', 'entity' => FormServerSolution::class, 'type' => 'boolean'),
    ];

    private const IMPORT_STATUS_ID = 6;

    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry|ManagerRegistry
     */
    private $registry;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
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
     * Run file import
     *
     * @param string $directory Absolute path to directory with CSV file(s)
     * @param int $formServerId The form server id
     */
    public function run(string $directory, int $formServerId): void
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
                    $data = file_get_contents($file);
                    if (!empty($data)) {
                        $rows = $this->getRowsFromCsvData($data);
                        $this->processImportRows($rows, $formServerId);
                    }
                }
            }
            $this->debug(sprintf('Finished import'));
        }
    }

    /**
     * Process content of the given CSV import rows
     *
     * @param array $rows The imported rows
     * @param int $formServerId Form server id for imported records
     */
    private function processImportRows(array $rows, int $formServerId): void
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();
        $expressionBuilder = $em->getExpressionBuilder();
        $status = $em->getRepository(Status::class)->find(self::IMPORT_STATUS_ID);
        $formServer = $em->getRepository(FormServer::class)->find($formServerId);
        $defaultMaturity = $em->getRepository(Maturity::class)->find(Maturity::DEFAULT_ID);
        if (null === $status || null === $formServer || null === $defaultMaturity) {
            $message = 'The default import values are not valid: ';
            if (null === $status) {
                $message .= ' default status is null ['.self::IMPORT_STATUS_ID.'];';
            }
            if (null === $formServer || null === $defaultMaturity) {
                $message .= ' default form server is null ['.$formServerId.'];';
            }
            if (null === $defaultMaturity) {
                $message .= ' default maturity is null ['.Maturity::DEFAULT_ID.'];';
            }
            $this->getLogger()->error($message);
            return;
        }
        $rowOffset = 0;
        /** @var Status $status */
        /** @var FormServer $formServer */
        /** @var Maturity $defaultMaturity */
        foreach ($rows as $importRow) {
            $solutionProperties = $importRow[Solution::class];
            $solution = $this->findEntityByConditions(Solution::class, [
                $expressionBuilder->eq('LOWER(e.name)', ':name')
                ], [
                    'name' => $solutionProperties['name']
                ]
            );
            $formServerSolution = null;
            if (null === $solution) {
                $solution = new Solution();
                $solution->setStatus($status);
                $solution->setImportSource('solution_importer');
                if (!empty($solutionProperties['importId'])) {
                    $solution->setImportId((int) $solutionProperties['importId']);
                }
                $em->persist($solution);
            } else {
                /** @var Solution $solution */
                $formServerSolutions = $solution->getFormServerSolutions();
                foreach ($formServerSolutions as $entity) {
                    if ($entity->getFormServer() === $formServer) {
                        $formServerSolution = $entity;
                        break;
                    }
                }
            }
            $this->debug('Saving solution: ' . $solutionProperties['name'] . ' [' . ($solution->getId() ?: 'NEW') . ']');
            $solution->setName($solutionProperties['name']);
            $this->addSpecializedProcedures($solution, $solutionProperties['specializedProcedures']);
            $this->addServiceSolutions($solution, $solutionProperties['serviceSolutions'], $defaultMaturity);
            if (null === $formServerSolution) {
                $formServerSolution = new FormServerSolution();
                $formServerSolution->setFormServer($formServer);
                $formServerSolution->setSolution($solution);
                $em->persist($formServerSolution);
                $solution->addFormServerSolution($formServerSolution);
            }
            $properties = $importRow[FormServerSolution::class];
            if (!empty($properties['articleNumber'])) {
                $formServerSolution->setArticleNumber((string) $properties['articleNumber']);
            }
            if (!empty($properties['assistantType'])) {
                $formServerSolution->setAssistantType((string) $properties['assistantType']);
            }
            if (!empty($properties['articleKey'])) {
                $formServerSolution->setArticleKey((string) $properties['articleKey']);
            }
            if (array_key_exists('usableAsPrintTemplate', $properties)) {
                $formServerSolution->setUsableAsPrintTemplate((bool) $properties['usableAsPrintTemplate']);
            }
            ++$rowOffset;
            if ($rowOffset % 100 === 0) {
                $em->flush();
            }
        }
        $em->flush();
    }

    private function addSpecializedProcedures(Solution $solution, array $importValues)
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();
        $expressionBuilder = $em->getExpressionBuilder();
        foreach ($importValues as $importValue) {
            $importEntity = $this->findEntityByConditions(SpecializedProcedure::class, [
                $expressionBuilder->eq('LOWER(e.name)', ':name')
            ], [
                    'name' => $importValue
                ]
            );
            if (null !== $importEntity) {
                /** @var SpecializedProcedure $importEntity */
                $solution->addSpecializedProcedure($importEntity);
            }
        }
    }

    private function addServiceSolutions(Solution $solution, array $importValues, Maturity $defaultMaturity)
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();
        $expressionBuilder = $em->getExpressionBuilder();
        foreach ($importValues as $importValue) {
            if (stripos($importValue, 'keine') === 0) {
                $importValue = 'nicht im LeiKa';
            }
            $service = $this->findEntityByConditions(Service::class, [
                $expressionBuilder->eq('LOWER(e.serviceKey)', ':value')
            ], [
                    'value' => $importValue
                ]
            );
            if (null !== $service) {
                /** @var Service $service */
                $serviceSolution = null;
                $serviceSolutions = $solution->getServiceSolutions();
                foreach ($serviceSolutions as $entity) {
                    if ($entity->getService() === $service) {
                        $serviceSolution = $entity;
                        break;
                    }
                }
                if (null === $serviceSolution) {
                    $serviceSolution = new ServiceSolution();
                    $serviceSolution->setService($service);
                    $serviceSolution->setSolution($solution);
                    $serviceSolution->setMaturity($defaultMaturity);
                    $em->persist($serviceSolution);
                    /** @var ServiceSolution $serviceSolution */
                    $solution->addServiceSolution($serviceSolution);
                }
            }
        }
    }

    /**
     * Create debug message with given verbosity
     *
     * @param string $message The message
     * @param int $verbosity The verbosity controls which messages are displayed
     */
    private function debug(string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
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
     * @param string $data
     * @return array
     */
    private function getRowsFromCsvData($data): array
    {
        $rows = array();
        $csvRows = explode("\n", $data);
        $firstRow = $csvRows[0];
        $possibleDelimiters = ['|', ';', ','];
        $delimiter = ';';
        foreach ($possibleDelimiters as $checkDelimiter) {
            if (strpos($firstRow, $checkDelimiter) !== false) {
                $delimiter = $checkDelimiter;
            }
        }
        $enclosing = strpos($firstRow, '"') === false ? '' : '"';

        $tmpHeaders = str_getcsv($firstRow, $delimiter, $enclosing);

        $headers = array();
        $parser = new DataParser();

        foreach ($tmpHeaders as $offset => $header) {
            if (empty($header)) {
                $headers[] = 'offset_' . $offset;
            } else {
                $headers[] = strtolower(
                    str_replace(['/', ' ', '-'],
                    '_',
                    $parser->cleanStringValue($parser->formatString($header)))
                );
            }
        }
        $rowCount = count($csvRows);
        // Skip header row, start count at 1
        for ($i = 1; $i < $rowCount; $i++) {
            $csvLine = trim($csvRows[$i]);
            if (!empty($csvLine)) {
                $row = $this->parseCsvLine($csvLine, $headers, $delimiter, $enclosing);
                if (!empty($row)) {
                    if (null !== $parsedRow = $this->fillInRowData($row)) {
                        $rows[] = $parsedRow;
                    }
                }
            }
        }
        return $rows;
    }

    /**
     * Fill in data for DB queries and updates/inserts.
     * PDO::prepare will escape parameters automatically later.
     *
     * @param array $row
     *
     * @return array|null Parsed row data
     */
    protected function fillInRowData($row): ?array
    {
        $entityPropertyData = [];
        $parser = new DataParser();
        foreach (self::FIELD_MAP as $srcField => $fieldData) {
            $val = null;
            if (!empty($fieldData['required']) && (!isset($row[$srcField]) || (string) $row[$srcField] === '')) {
                return null;
            }
            $trgField = $fieldData['field'];
            if (isset($row[$srcField])) {
                if (array_key_exists('fixedValue', $fieldData)) {
                    $val = $fieldData['fixedValue'];
                } else {
                    $val = $row[$srcField];
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
     * Either find an existing entity by the given field or create a new entity
     * @param string $entityClass
     * @param array $expressions
     * @param array $parameters
     * @return BaseEntity|null
     */
    private function findEntityByConditions(string $entityClass, array $expressions, array $parameters = []): ?BaseEntity
    {
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository($entityClass);
        $qb = $repository->createQueryBuilder('e')
            ->orderBy('e.id', 'ASC');
        $andX = $or = $qb->expr()->andX();
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
     * @param string $csvLine Raw csv line content
     * @param array $headers CSV headers found in the first line of the csv file
     * @param string $delimiter CSV delimiter
     * @param string $enclosure
     * @return array|null
     */
    private function parseCsvLine(string $csvLine, array $headers, string $delimiter, string $enclosure): ?array
    {
        $csvRow = str_getcsv($csvLine, $delimiter, $enclosure);
        $row = array();
        foreach ($headers as $offset => $header) {
            if (isset($csvRow[$offset])) {
                $row[$header] = trim($csvRow[$offset]);
            } else {
                $errMsg = sprintf('CSV row column count does not match header column count: %s', $csvLine);
                $this->debug($errMsg, OutputInterface::VERBOSITY_VERBOSE);
                return null;
            }
        }
        return $row;
    }
}
