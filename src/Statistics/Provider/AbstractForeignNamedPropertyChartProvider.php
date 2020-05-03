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

namespace App\Statistics\Provider;

use App\Statistics\AbstractChartJsStatisticsProvider;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractForeignNamedPropertyChartProvider extends AbstractChartJsStatisticsProvider
{

    /**
     * Provider type (excel|csv|chart)
     * @var string
     */
    protected $type = 'chart';

    protected $chartType = 'pie';

    protected $chartLabel = 'Anzahl der Datensätze';
    protected $foreignProperty = 'status';
    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry|ManagerRegistry
     */
    private $registry;

    /**
     * @required
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
     */
    public function injectSolutionManager(ManagerRegistry $registry): void
    {
        $this->registry = $registry;
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
            'label' => $this->chartLabel,
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
     * Returns the entity class
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * @inheritDoc
     */
    protected function loadData()
    {
        $property = 's.' . $this->foreignProperty;
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository($this->getEntityClass());
        $queryBuilder = $repository->createQueryBuilder('s');
        $queryBuilder
            ->select('COUNT(s.id) AS itemCount', 'IDENTITY(' . $property . ') AS refId', 'fnp.name')
            ->leftJoin($property, 'fnp')
            ->groupBy($property)
            ->orderBy($property);
        //$queryBuilder->
        $query = $queryBuilder->getQuery();
        $result = $query->getArrayResult();
        $data = [];
        foreach ($result as $row) {
            $key = (string)$row['name'] !== '' ? $row['name'] : 'n.a';
            $data[$key] = $row['itemCount'];
        }
        return $data;
    }
}