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

namespace App\Statistics;

/**
 * Interface for export statistics providers
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 */
interface ExportStatisticsProviderInterface
{

    /**
     * Returns the provider export data
     *
     * @return array
     */
    public function getExportData();

    /**
     * Returns the provider export options
     *
     * @return array
     */
    public function getExportOptions();
}