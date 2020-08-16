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

use App\Entity\ImplementationProject;
use Doctrine\ORM\QueryBuilder;

class ImplementationProjectStatusChartProvider extends AbstractForeignNamedPropertyChartProvider
{

    protected $chartLabel = 'Anzahl der Umsetzungsprojekte';

    protected function getEntityClass(): string
    {
        return ImplementationProject::class;
    }

    protected function addCustomDataConditions(QueryBuilder $queryBuilder, string $alias = 's'): void
    {
        if (!empty($this->additionalFilters)) {
            foreach ($this->additionalFilters as $filterProperty => $filterValue) {
                if ($filterProperty === 'subject') {
                    $queryBuilder->leftJoin( $alias . '.serviceSystems', 'ssy');
                    $queryBuilder->leftJoin( 'ssy.situation', 'st');
                    $queryBuilder->andWhere($queryBuilder->expr()->eq('st.' . $filterProperty, ':' . $filterProperty));
                    $queryBuilder->setParameter($filterProperty, trim(strip_tags($filterValue)));
                } else {
                    $queryBuilder->andWhere($queryBuilder->expr()->eq('s.' . $filterProperty, ':' . $filterProperty));
                    $queryBuilder->setParameter($filterProperty, trim(strip_tags($filterValue)));
                }
            }
        }
    }
}