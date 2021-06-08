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

namespace App\Statistics\Provider;

use App\Entity\Solution;

class SolutionMaturityChartProvider extends AbstractForeignNamedPropertyChartProvider
{

    protected $chartLabel = 'Anzahl der Online-Dienste';
    protected $foreignProperty = 'maturity';
    protected $foreignColorProperty = 'color';

    protected $tooltipsOptions = [
        // https://www.chartjs.org/docs/latest/general/interactions/modes.html#interaction-modes
        //'mode' => 'index',
        //'intersect' => false,
        // Show participant count instead of average result in tooltips; store participant count in custom
        // variable baTooltipLabels
        'callbacks' => [
            'label' => 'function(item, data) {
                var label = item.xLabel ? item.xLabel : data.labels[item.index];
                var count = item.yLabel ? item.yLabel : data.datasets[item.datasetIndex].data[item.index];
                var value = label === "n.a" ? "Kein Reifegrad" : "Reifegrad " + label;
                value += ": " + count + " Online-Dienste";
                return value;
            }',
        ],
    ];

    protected function getEntityClass(): string
    {
        return Solution::class;
    }
}