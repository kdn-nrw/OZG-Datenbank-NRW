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

use App\Entity\StateGroup\Commune;
use App\Import\DataProcessor\CommuneDataProcessor;
use App\Import\Model\CommuneImportModel;

class CommuneImporter extends AbstractImporter
{
    /**
     * Returns the import source key
     *
     * @return string
     */
    protected function getImportSourceKey(): string
    {
        return 'commune_importer';
    }

    /**
     * @required
     * @param CommuneDataProcessor $dataProcessor
     */
    public function injectDataProcessor(CommuneDataProcessor $dataProcessor): void
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
        return CommuneImportModel::class;
    }
}
