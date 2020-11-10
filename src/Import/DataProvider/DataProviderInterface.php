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

use App\Import\DataProcessor\DataProcessorInterface;

interface DataProviderInterface
{
    /**
     * Load and process the data
     *
     * @param DataProcessorInterface $dataProcessor
     * @return int The number of records loaded
     */
    public function process(DataProcessorInterface $dataProcessor): int;
}
