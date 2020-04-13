<?php

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