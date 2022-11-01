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

use App\Import\Model\ResultCollection;
use ReflectionException;
use Symfony\Component\Console\Output\OutputInterface;

interface DataProcessorInterface
{
    /**
     * @param string $importModelClass
     */
    public function setImportModelClass(string $importModelClass): void;

    /**
     * @param OutputInterface|null $output
     */
    public function setOutput(?OutputInterface $output): void;

    /**
     * Sets the import source key
     * @param string $importSource
     */
    public function setImportSource(string $importSource): void;

    /**
     * Process content of the loaded import rows
     */
    public function processImportedRows(): void;

    /**
     * Fill in data for DB queries and updates/inserts.
     * PDO::prepare will escape parameters automatically later.
     *
     * @param array $row
     * @param int $rowNr
     *
     * @return void
     */
    public function addRecordRaw(array $row, int $rowNr): void;


    /**
     * Add custom base data to result collection
     *
     * @param array $data
     * @throws ReflectionException
     */
    public function addBaseResultData(array $data): void;

    /**
     * Initialize the result collection
     * @return ResultCollection
     */
    public function getResultCollection(): ResultCollection;

    /**
     * Unset the result collection
     */
    public function unsetResultCollection(): void;
}
