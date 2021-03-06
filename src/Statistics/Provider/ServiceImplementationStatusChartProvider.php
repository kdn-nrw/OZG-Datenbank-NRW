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

use App\Entity\Service;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ServiceImplementationStatusChartProvider extends AbstractForeignNamedPropertyChartProvider
{

    protected $chartLabel = 'Kommunale Leistungen: Umsetzungs-Status in NRW';
    protected $foreignColorProperty = 'color';

    protected function getEntityClass(): string
    {
        return Service::class;
    }

    /**
     * @inheritDoc
     */
    protected function loadData()
    {
        /** @var EntityRepository $repository */
        $repository = $this->getEntityManager()->getRepository($this->getEntityClass());
        $entities = $repository->findAll();
        $result = [];
        $filterJurisdictions = [];
        if (!empty($this->additionalFilters['jurisdictions'])) {
            $filterJurisdictions = explode(',', $this->additionalFilters['jurisdictions']);
        }
        foreach ($entities as $entity) {
            /** @var Service $entity */
            $isFilteredOut = false;
            if (!empty($filterJurisdictions)) {
                $isFilteredOut = true;
                $jurisdictions = $entity->getJurisdictions();
                foreach ($jurisdictions as $jurisdiction) {
                    if (in_array($jurisdiction->getId(), $filterJurisdictions, false)) {
                        $isFilteredOut = false;
                        break;
                    }
                }
            }
            if ($isFilteredOut) {
                continue;
            }
            $statusInfo = $entity->getImplementationProjectStatusInfo();
            $status = $statusInfo->getStatus();
            if (null !== $status) {
                if (!isset($result[$status->getId()])) {
                    $result[$status->getId()] = [
                        'itemCount' => 1,
                        'refId' => $status->getId(),
                        'name' => $status->getName(),
                        'color' => $status->getColor(),
                    ];
                } else {
                    ++$result[$status->getId()]['itemCount'];
                }
            }
        }
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
                if ($filterProperty === 'jurisdiction') {
                    $queryBuilder->leftJoin($alias . '.serviceSystems', 'ssy');
                    $queryBuilder->leftJoin('ssy.situation', 'st');
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