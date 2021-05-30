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

namespace App\Controller;


use App\Admin\Frontend\ImplementationProjectAdmin;
use App\Entity\ImplementationProject;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class ImplementationProjectController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-24
 */
class ImplementationProjectController extends AbstractFrontendCRUDController
{
    /**
     * @inheritDoc
     */
    protected function getDefaultRouteName(): string
    {
        return 'frontend_app_implementationproject_list';
    }

    /**
     * @inheritDoc
     */
    protected function getAdminClassName(): string
    {
        return ImplementationProjectAdmin::class;
    }

    public function timelineAction(Request $request)
    {
        $now = date_create('first day of this month midnight');
        $now->setTimezone(new \DateTimeZone('UTC'));
        /** @var ModelManager $modelManager */
        $modelManager = $this->admin->getModelManager();
        $em = $modelManager->getEntityManager(ImplementationProject::class);
        $repository = $em->getRepository(ImplementationProject::class);
        $queryBuilder = $repository->createQueryBuilder('p');
        $queryBuilder->where('p.commissioningStatusAt > :minTime OR p.nationwideRolloutAt > :minTimeNationwide');
        $queryBuilder->setParameter('minTime', $now);
        $queryBuilder->setParameter('minTimeNationwide', $now);
        $entities = $queryBuilder->getQuery()->getResult();
        $groupedSubjectsByMonths = [
            'commissioningStatusAt' => [],
            'nationwideRolloutAt' => [],
        ];
        $min = (int)date('Ym');
        $max = [
            'commissioningStatusAt' => $min,
            'nationwideRolloutAt' => $min,
        ];
        foreach ($entities as $entity) {
            /** @var ImplementationProject $entity */
            $dates = [
                'commissioningStatusAt' => $entity->getCommissioningStatusAt(),
                'nationwideRolloutAt' => $entity->getNationwideRolloutAt(),
            ];
            foreach ($dates as $property => $propertyDate) {
                /** \DateTime $date */
                $monthKey = $this->addGroupedSubject($groupedSubjectsByMonths, $entity, $propertyDate, $property);
                if ($monthKey && $monthKey > $max[$property]) {
                    $max[$property] = $monthKey;
                }
            }
        }
        $tabs = [
            'commissioningStatusAt' => 'app.implementation_project.frontend.tab_commissioning_status_at',
            'nationwideRolloutAt' => 'app.implementation_project.frontend.tab_nationwide_rollout_at',
        ];
        $parameters = [
            'tabs' => $tabs,
            'sortedResultsByProperty' => $this->sortResults($groupedSubjectsByMonths, $min, $max),
        ];
        $response = $this->renderWithExtraParams('ImplementationProjectAdmin/timeline.html.twig', $parameters);
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'type' => 'content',
                'html' => trim($response->getContent()),
            ]);
        }
        return $response;
    }

    private function sortResults(array $groupedSubjectsByMonths, int $min, array $max): array
    {
        $sortedResults = [];
        $maxItems = 24;
        foreach ($max as $property => $propertyMax) {
            $itemCount = 1;
            if (!empty($groupedSubjectsByMonths[$property])) {
                $propertyGroupedData = $groupedSubjectsByMonths[$property];
                $month = date('n');
                $year = date('Y');
                $monthKey = $min;
                while ($monthKey <= $propertyMax) {
                    if (array_key_exists($monthKey, $propertyGroupedData)) {
                        ksort($propertyGroupedData[$monthKey]);
                        $sortedResults[$property][$monthKey] = [
                            'date' => date_create($year . '-' . $month . '-01 00:00:00'),
                            'subjects' => $propertyGroupedData[$monthKey],
                        ];
                    } else {
                        $sortedResults[$property][$monthKey] = [
                            'date' => date_create($year . '-' . $month . '-01 00:00:00'),
                            'subjects' => [],
                        ];
                    }
                    ++$itemCount;
                    ++$month;
                    if ($month > 12) {
                        $month = 1;
                        ++$year;
                    }
                    $monthKey = $year . ($month < 10 ? '0' . $month : $month);
                    if ($itemCount > $maxItems) {
                        break;
                    }
                }
            }
        }
        return $sortedResults;
    }

    private function addGroupedSubject(array &$groupedSubjectsByMonths, ImplementationProject $entity, ?\DateTime $dateTime, string $property): ?int
    {
        $monthKey = null;
        if (null !== $dateTime) {
            $monthKey = (int)date('Ym', $dateTime->getTimestamp());
            if ($monthKey >= (int)date('Ym')) {
                $subjects = $entity->getSubjects();
                $iconPath = '/img/icons/subject/16/';
                foreach ($subjects as $subject) {
                    if (!isset($groupedSubjectsByMonths[$property][$monthKey][$subject->getId()])) {
                        $choiceIconPath = $subject->getId() < 16 ? $iconPath : null;
                        $groupedSubjectsByMonths[$property][$monthKey][$subject->getId()] = [
                            'id' => $subject->getId(),
                            'name' => $subject->getName(),
                            'icon' => $choiceIconPath ? $choiceIconPath . $subject->getId() . '.png' : null,
                            'items' => [],
                        ];
                    }
                    $groupedSubjectsByMonths[$property][$monthKey][$subject->getId()]['items'][$entity->getId()] = $entity;
                }
            }
        }
        return $monthKey;
    }
}
