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

namespace App\Import\DataProvider;

use App\Import\DataParser;
use App\Import\DataProcessor\DataProcessorInterface;
use App\Import\OutputInterfaceTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Output\OutputInterface;

class CsvDataProvider implements DataProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use OutputInterfaceTrait;

    /**
     * The data source: absolute path to directory with CSV file(s)
     *
     * @var string
     */
    private $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Load and process the data
     *
     * @param DataProcessorInterface $dataProcessor
     * @return int The number of records loaded
     */
    public function process(DataProcessorInterface $dataProcessor): int
    {
        $recordCount = 0;
        $directory = $this->directory;
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
                        $recordCount += $this->processFileResource($handle, $dataProcessor);
                        fclose($handle);
                    }
                    $this->debug(sprintf('Finished importing raw data from %s', $file));
                }
            }
        }
        return $recordCount;
    }

    /**
     * Converts the given file data to an array with field value rows
     *
     * @param resource $handle
     * @param DataProcessorInterface $dataProcessor
     * @return int Number of loaded rows
     */
    private function processFileResource($handle, DataProcessorInterface $dataProcessor): int
    {
        $delimiter = null;
        $possibleDelimiters = ['|', ';', ','];
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
                $headers[] = $parser->getCleanFieldName($header);
            }
        }
        $rowNr = 0;
        while (($data = fgetcsv($handle, 10000, $delimiter, $enclosure)) !== FALSE) {
            ++$rowNr;
            $row = $this->parseCsvLine($data, $headers);
            if (!empty($row)) {
                $dataProcessor->addRecordRaw($row, $rowNr);
            }
        }
        return $rowNr;
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
        $row = [];
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
