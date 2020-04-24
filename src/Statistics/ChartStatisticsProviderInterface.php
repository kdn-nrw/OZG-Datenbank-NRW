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
 * Interface for chart statistics providers
 */
interface ChartStatisticsProviderInterface
{

    /**
     * Returns the chart configuration
     *
     * @return array
     */
    public function getChartConfig();

    /**
     * Returns the list of JavaScript files required for this provider
     * @return array
     */
    public function getScripts();
}