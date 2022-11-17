<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Statistics\Provider;

use App\Entity\ModelRegion\ModelRegionProject;
use App\Translator\InjectTranslatorTrait;
use Doctrine\ORM\EntityRepository;

class ModelRegionProjectStatusChartProvider extends AbstractForeignNamedPropertyChartProvider
{
    use InjectTranslatorTrait;

    protected $chartLabel = 'Anzahl der DMR Umsetzungsprojekte';
    protected $foreignColorProperty = 'color';

    protected function getEntityClass(): string
    {
        return ModelRegionProject::class;
    }

    /**
     * @inheritDoc
     */
    protected function loadData()
    {
        $alias = 's';
        $em = $this->getEntityManager();
        /** @var EntityRepository $repository */
        $repository = $em->getRepository($this->getEntityClass());
        $queryBuilder = $repository->createQueryBuilder($alias);
        $selects = ['COUNT(s.status) AS groupCount', 's.status AS groupKey'];
        $queryBuilder
            ->select($selects);
        $this->addCustomDataConditions($queryBuilder, $alias);
        $queryBuilder
            ->groupBy('s.status')
            ->orderBy('s.status');
        $query = $queryBuilder->getQuery();
        $result = $query->getArrayResult();
        $tmpLabels = ModelRegionProject::$statusChoices;
        $labels = [];
        $data = [];
        foreach ($tmpLabels as $choiceId => $choiceLabel) {
            $dataLabel = $this->translator->trans($choiceLabel);
            $data[$dataLabel] = 0;
            $labels[$choiceId] = $dataLabel;
        }
        foreach ($result as $row) {
            /** @var ModelRegionProject $entity */
            $status = (int) $row['groupKey'];
            if (isset($labels[$status])) {
                $key = $labels[$status];
                $data[$key] = (int) $row['groupCount'];
            }
        }
        // Remove "not started" entry, if all projects have started
        if (empty($data[$labels[ModelRegionProject::STATUS_NOT_STARTED]])) {
            unset($data[$labels[ModelRegionProject::STATUS_NOT_STARTED]]);
        }
        $rowCount = count($data);
        $colorOffset = 0;
        $colorCount = count(self::$defaultColors);

        for ($i = 0; $i < $rowCount; $i++) {
            $rowColor = self::$defaultColors[$colorOffset];
            ++$colorOffset;
            if ($colorOffset >= $colorCount) {
                $colorOffset = 0;
            }
            $this->colors[] = $rowColor;
        }
        return $data;
    }
}