<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Statistics\Provider;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\StateGroup\DataCenterConsumption;
use App\Statistics\AbstractChartJsStatisticsProvider;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class DataCenterConsumptionChartProvider extends AbstractChartJsStatisticsProvider
{
    use InjectManagerRegistryTrait;

    protected $chartLabel = 'Rechenzentrum Energieverbrauch';

    protected $chartType = 'line';

    protected $mainProperty = 'year';

    protected function getEntityClass(): string
    {
        return DataCenterConsumption::class;
    }

    /**
     * @inheritDoc
     */
    protected function createChartData(): array
    {
        $groupedData = $this->loadData();
        $xAxisLabels = array_keys($groupedData);

        $dataSetConfiguration = [];
        if (empty($this->colors)) {
            $this->colors = self::$defaultColors;
        }
        $offset = 0;
        $dataSetConfiguration[$offset] = [
            'label' => $this->chartLabel,
            'backgroundColor' => $this->colors[0],
            'data' => [],
        ];
        foreach ($groupedData as $data) {
            $dataSetConfiguration[$offset]['data'][] = $data;
        }
        $chartData = [
            'labels' => array_values($xAxisLabels),
            'datasets' => $dataSetConfiguration,
        ];
        return $chartData;
    }

    protected function addCustomDataConditions(QueryBuilder $queryBuilder, string $alias = 's'): void
    {
        if (!empty($this->additionalFilters)) {
            foreach ($this->additionalFilters as $filterProperty => $filterValue) {
                if ($filterProperty === 'serviceProvider') {
                    $queryBuilder->leftJoin( $alias . '.dataCenter', 'ds');
                    $queryBuilder->andWhere($queryBuilder->expr()->eq('ds.' . $filterProperty, ':' . $filterProperty));
                    $queryBuilder->setParameter($filterProperty, trim(strip_tags($filterValue)));
                } else {
                    $queryBuilder->andWhere($queryBuilder->expr()->eq('s.' . $filterProperty, ':' . $filterProperty));
                    $queryBuilder->setParameter($filterProperty, trim(strip_tags($filterValue)));
                }
            }
        }
    }
    /**
     * @inheritDoc
     */
    protected function loadData()
    {
        $alias = 's';
        $property = $alias . '.' . $this->mainProperty;
        /** @var EntityRepository $repository */
        $repository = $this->getEntityManager()->getRepository($this->getEntityClass());
        $queryBuilder = $repository->createQueryBuilder($alias);
        $selects = ['s.year', 'SUM(s.powerConsumption) AS itemSum',];
        $queryBuilder
            ->select($selects)
            ->where('s.year IS NOT NULL');
        $this->addCustomDataConditions($queryBuilder, $alias);
        $queryBuilder
            ->groupBy($property)
            ->orderBy($property);
        $query = $queryBuilder->getQuery();
        $result = $query->getArrayResult();
        $data = [];
        foreach ($result as $row) {
            $key = $row['year'];
            $data[$key] = $row['itemSum'];
        }
        return $data;
    }
}