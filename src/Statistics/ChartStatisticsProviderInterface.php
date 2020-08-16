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
     * Add custom filter as JSON encoded string
     *
     * @param string|null $filters
     * @return void
     */
    public function addFilters(?string $filters): void;

    /**
     * Returns the chart configuration
     *
     * @return array
     */
    public function getChartConfig(): array;

    /**
     * Returns the list of JavaScript files required for this provider
     * @return array
     */
    public function getScripts(): array;
}