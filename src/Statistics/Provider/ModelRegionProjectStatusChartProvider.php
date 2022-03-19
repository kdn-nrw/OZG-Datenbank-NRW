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
        /** @var EntityRepository $repository */
        $repository = $this->getEntityManager()->getRepository($this->getEntityClass());
        $queryBuilder = $repository->createQueryBuilder($alias);
        $selects = ['s.projectStartAt', 's.projectConceptStartAt', 's.projectImplementationStartAt', 's.projectEndAt',];
        $queryBuilder
            ->select($selects);
        $this->addCustomDataConditions($queryBuilder, $alias);
        $queryBuilder
            ->orderBy('s.id');
        $query = $queryBuilder->getQuery();
        $result = $query->getArrayResult();
        $labels = [
            0 => $this->translator->trans('app.model_region_project.entity.project_not_started'),
            1 => $this->translator->trans('app.model_region_project.entity.project_start_at'),
            2 => $this->translator->trans('app.model_region_project.entity.project_concept_start_at'),
            3 => $this->translator->trans('app.model_region_project.entity.project_implementation_start_at'),
            4 => $this->translator->trans('app.model_region_project.entity.project_end_at'),
        ];
        $data = [];
        foreach ($labels as $key) {
            $data[$key] = 0;
        }
        $now = date_create();
        $now->setTimezone(new \DateTimeZone('UTC'));
        foreach ($result as $row) {
            if (null !== $row['projectEndAt'] && $row['projectEndAt'] < $now) {
                $status = 4;
            } elseif (null !== $row['projectImplementationStartAt'] && $row['projectImplementationStartAt'] < $now) {
                $status = 3;
            } elseif (null !== $row['projectConceptStartAt'] && $row['projectConceptStartAt'] < $now) {
                $status = 2;
            } elseif (null !== $row['projectStartAt'] && $row['projectStartAt'] < $now) {
                $status = 1;
            } else {
                $status = 0;
            }
            $key = $labels[$status];
            ++$data[$key];
        }
        // Remove "not started" entry, if all projects have started
        if (empty($data[$labels[0]])) {
            unset($data[$labels[0]]);
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