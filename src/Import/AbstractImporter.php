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

use App\Import\DataProcessor\DataProcessorInterface;
use App\Import\DataProvider\DataProviderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

abstract class AbstractImporter implements LoggerAwareInterface, HasImportModelInterface
{
    use LoggerAwareTrait;
    use OutputInterfaceTrait;

    /**
     * @var DataProcessorInterface
     */
    protected $dataProcessor;

    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Run import
     *
     * @param DataProviderInterface $dataProvider The import data provider
     */
    public function run(DataProviderInterface $dataProvider): int
    {
        $modelClass = $this->getImportModelClass();
        $this->dataProcessor->setImportModelClass($modelClass);
        $this->dataProcessor->setOutput($this->output);
        $this->dataProcessor->setImportSource($this->getImportSourceKey());
        $importRowCount = $dataProvider->process($this->dataProcessor);
        $this->dataProcessor->processImportedRows();
        return $importRowCount;
    }

    /**
     * Returns the import source key
     *
     * @return string
     */
    abstract protected function getImportSourceKey(): string;
}
