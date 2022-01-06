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

namespace App\Import;

use App\Import\DataProcessor\EFileDataProcessor;
use App\Import\Model\EFileImportModel;

class EFileImporter extends AbstractImporter
{

    /**
     * Returns the import source key
     *
     * @return string
     */
    protected function getImportSourceKey(): string
    {
        return 'efile_importer';
    }

    /**
     * @required
     * @param EFileDataProcessor $dataProcessor
     */
    public function injectDataProcessor(EFileDataProcessor $dataProcessor): void
    {
        $this->dataProcessor = $dataProcessor;
    }

    /**
     * Returns the import model class for this importer
     *
     * @return string
     */
    public function getImportModelClass(): string
    {
        return EFileImportModel::class;
    }
}
