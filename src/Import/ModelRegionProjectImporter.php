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

use App\Entity\ModelRegionProject;
use App\Import\DataProcessor\ModelRegionProjectDataProcessor;
use App\Import\Model\ModelRegionProjectImportModel;

class ModelRegionProjectImporter extends AbstractImporter
{

    protected function getImportSourceKey(): string
    {
        return 'model_region_project_importer';
    }

    /**
     * @required
     * @param ModelRegionProjectDataProcessor $dataProcessor
     */
    public function injectDataProcessor(ModelRegionProjectDataProcessor $dataProcessor): void
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
        return ModelRegionProjectImportModel::class;
    }
}
