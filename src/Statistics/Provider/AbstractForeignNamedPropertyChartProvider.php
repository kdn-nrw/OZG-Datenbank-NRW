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

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Statistics\AbstractChartJsStatisticsProvider;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractForeignNamedPropertyChartProvider extends AbstractChartJsStatisticsProvider
{
    use InjectManagerRegistryTrait;

    /**
     * Provider type (excel|csv|chart)
     * @var string
     */
    protected $type = 'chart';

    protected $chartType = 'pie';

    protected $chartLabel = 'Anzahl der Datensätze';
    protected $foreignProperty = 'status';

    /**
     * Optional foreign property for color value
     * @var string|null
     */
    protected $foreignColorProperty;

    /**
     * @inheritDoc
     */
    protected function createChartData()
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
        $alias = 's';
        $property = $alias . '.' . $this->foreignProperty;
        /** @var EntityRepository $repository */
        $repository = $this->getEntityManager()->getRepository($this->getEntityClass());
        $queryBuilder = $repository->createQueryBuilder($alias);
        $selects = ['COUNT(s.id) AS itemCount', 'IDENTITY(' . $property . ') AS refId', 'fnp.name'];
        if ($this->foreignColorProperty) {
            $selects[] = 'fnp.' . $this->foreignColorProperty;
        }
        $queryBuilder
            ->select($selects)
            ->leftJoin($property, 'fnp');
        $this->addCustomDataConditions($queryBuilder, $alias);
        $queryBuilder
            ->groupBy($property)
            ->orderBy($property);
        //$queryBuilder->
        $query = $queryBuilder->getQuery();
        $result = $query->getArrayResult();
        $data = [];
        $colorOffset = 0;
        $disabledColorOffset = 0;
        $colorCount = count(self::$defaultColors);
        foreach ($result as $row) {
            $isNamed = (string)$row['name'] !== '';
            $key = $isNamed ? $row['name'] : 'n.a';
            $data[$key] = $row['itemCount'];
            if ($this->foreignColorProperty && !empty($row[$this->foreignColorProperty])) {
                $rowColor = $row[$this->foreignColorProperty];
            } elseif (!$isNamed) {
                $rowColor = $this->disabledColors[$disabledColorOffset];
                ++$disabledColorOffset;
                if ($disabledColorOffset >= count($this->disabledColors)) {
                    $disabledColorOffset = 0;
                }
            } else {
                $rowColor = self::$defaultColors[$colorOffset];
                ++$colorOffset;
                if ($colorOffset >= $colorCount) {
                    $colorOffset = 0;
                }
            }
            $this->colors[] = $rowColor;
        }
        return $data;
    }

    protected function addCustomDataConditions(QueryBuilder $queryBuilder, string $alias = 's'): void
    {
        if (!empty($this->additionalFilters)) {
            foreach ($this->additionalFilters as $filterProperty => $filterValue) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq($alias . '.' . $filterProperty, ':' . $filterProperty));
                $queryBuilder->setParameter($filterProperty, trim(strip_tags($filterValue)));
            }
        }
    }
}