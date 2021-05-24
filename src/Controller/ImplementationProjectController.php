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
        $queryBuilder->where('p.commissioningStatusAt > :minTime');
        $queryBuilder->setParameter('minTime', $now);
        $entities = $queryBuilder->getQuery()->getResult();
        $groupedSubjectsByMonths = [];
        $min = (int)date('Ym');
        $max = $min;
        $iconPath = '/img/icons/subject/16/';
        foreach ($entities as $entity) {
            /** @var ImplementationProject $entity */
            $dateTime = $entity->getCommissioningStatusAt();
            if (null !== $dateTime) {
                $monthKey = (int)date('Ym', $dateTime->getTimestamp());
                if ($monthKey > $max) {
                    $max = $monthKey;
                }
                $subjects = $entity->getSubjects();
                foreach ($subjects as $subject) {
                    if (!isset($groupedSubjectsByMonths[$monthKey][$subject->getId()])) {
                        $choiceIconPath = $subject->getId() < 16 ? $iconPath : null;
                        $groupedSubjectsByMonths[$monthKey][$subject->getId()] = [
                            'id' => $subject->getId(),
                            'name' => $subject->getName(),
                            'icon' => $choiceIconPath ? $choiceIconPath . $subject->getId() . '.png' : null,
                            'items' => [],
                        ];
                    }
                    $groupedSubjectsByMonths[$monthKey][$subject->getId()]['items'][$entity->getId()] = $entity;
                }
            }
        }
        $sortedResults = [];
        $maxItems = 24;
        $itemCount = 1;
        if (!empty($groupedSubjectsByMonths)) {
            $month = date('n');
            $year = date('Y');
            $monthKey = $min;
            while ($monthKey <= $max) {
                if (array_key_exists($monthKey, $groupedSubjectsByMonths)) {
                    ksort($groupedSubjectsByMonths[$monthKey]);
                    $sortedResults[$monthKey] = [
                        'date' => date_create($year . '-' . $month . '-01 00:00:00'),
                        'subjects' => $groupedSubjectsByMonths[$monthKey],
                    ];
                } else {
                    $sortedResults[$monthKey] = [
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
        $parameters = [
            'sortedResults' => $sortedResults,
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
}
