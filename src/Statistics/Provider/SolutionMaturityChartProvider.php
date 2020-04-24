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

use App\Entity\Manager\SolutionManager;
use App\Entity\Solution;
use App\Statistics\AbstractChartJsStatisticsProvider;
use Doctrine\DBAL\Connection;

class SolutionMaturityChartProvider extends AbstractChartJsStatisticsProvider {

    /**
     * @var SolutionManager
     */
    private $solutionManager;

    /**
     * Provider type (excel|csv|chart)
     * @var string
     */
    protected $type = 'chart';

    protected $chartType = 'bar';

    protected $tooltipsOptions = [
        'mode' => 'index',
        'intersect' => false,
        // Show participant count instead of average result in tooltips; store participant count in custom
        // variable baTooltipLabels
        'callbacks' => [
            'label' => 'function(item, data) {
                        var value = item.xLabel === "n.a" ? "Kein Reifegrad" : "Reifegrad " + item.xLabel;
                        value += ": " + item.yLabel + " Lösungen";
                        return value;
                    }',
        ],
    ];

    /**
     * @required
     * @param SolutionManager $solutionManager
     */
    public function injectSolutionManager(SolutionManager $solutionManager): void
    {
        $this->solutionManager = $solutionManager;
    }

    /**
     * @inheritDoc
     */
    protected function createChartData()
    {
        $groupedData = $this->loadData();
        $xAxisLabels = array_keys($groupedData);

        $dataSetConfiguration = [];
        $offset = 0;
        $dataSetConfiguration[$offset] = [
            'label' => 'Anzahl der Lösungen',
            'backgroundColor' => $this->colors,
            'data' => [],
        ];
        foreach ($groupedData as $key => $data) {
            $dataSetConfiguration[$offset]['data'][] = $data;
        }
        $chartData = [
            'labels' => array_values($xAxisLabels),
            'datasets' => $dataSetConfiguration,
        ];
        return $chartData;
    }

    /**
     * @inheritDoc
     */
    protected function loadData()
    {
        /** @var Connection $connection */
        $queryBuilder = $this->solutionManager->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->from(Solution::class, 's')
            ->select('s.id', 'IDENTITY(s.maturity) AS maturityId', 'm.name')
            ->leftJoin('s.maturity', 'm')
            ->orderBy('s.maturity')
            ->addOrderBy('s.id');
        //$queryBuilder->
        $query = $queryBuilder->getQuery();
        $result = $query->getArrayResult();
        $data = [];
        foreach ($result as $row) {
            $key = (string) $row['name'] !== '' ? $row['name'] : 'n.a';
            if (!isset($data[$key])) {
                $data[$key] = 0;
            }
            ++$data[$key];
        }
        return $data;
    }
}