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

class SolutionStatusChartProvider extends AbstractChartJsStatisticsProvider {

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
            ->select('s.id', 'IDENTITY(s.status) AS statusId', 'st.name')
            ->leftJoin('s.status', 'st')
            ->orderBy('s.status')
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