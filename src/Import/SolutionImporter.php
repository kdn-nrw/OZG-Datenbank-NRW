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

use App\Import\DataProcessor\SolutionDataProcessor;
use App\Import\Model\SolutionImportModel;

class SolutionImporter extends AbstractImporter
{
    protected function getImportSourceKey(): string
    {
        return 'solution_importer';
    }

    /**
     * Sets the maturity id
     *
     * @param int $id
     */
    public function setMaturityId(int $id): void
    {
        /** @var SolutionDataProcessor $dataProcessor */
        $dataProcessor = $this->dataProcessor;
        $dataProcessor->setMaturityById($id);
    }

    /**
     * Sets the form server id
     *
     * @param int $id
     */
    public function setFormServerById(int $id): void
    {
        /** @var SolutionDataProcessor $dataProcessor */
        $dataProcessor = $this->dataProcessor;
        $dataProcessor->setFormServerById($id);
    }

    /**
     * @required
     * @param SolutionDataProcessor $dataProcessor
     */
    public function injectDataProcessor(SolutionDataProcessor $dataProcessor): void
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
        return SolutionImportModel::class;
    }
}
