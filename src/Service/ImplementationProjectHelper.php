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

namespace App\Service;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\ImplementationProject;
use App\Entity\ImplementationProjectService;
use App\Entity\ImplementationStatus;
use App\Model\ImplementationStatusInfoInterface;
use Doctrine\ORM\EntityRepository;

class ImplementationProjectHelper
{
    use InjectManagerRegistryTrait;

    /**
     * @var array
     */
    protected $statusChoices;

    /**
     * Returns the status choices
     * @return array
     */
    private function getStatusChoices(): array
    {
        if (null === $this->statusChoices) {
            /** @var EntityRepository $repository */
            $repository = $this->getEntityManager()->getRepository(ImplementationStatus::class);
            $this->statusChoices = $repository->findBy(['setAutomatically' => 1], ['level' => 'ASC']);
        }
        return $this->statusChoices;
    }

    /**
     * Returns the status info for the given implementation project
     *
     * @param ImplementationStatusInfoInterface $statusInfoModel
     * @return array
     */
    public function getProjectStatusInfo(ImplementationStatusInfoInterface $statusInfoModel): array
    {
        $statusChoices = $this->getStatusChoices();
        $statusInfo = [];
        foreach ($statusChoices as $status) {
            /** @var ImplementationStatus $status */
            $isCurrent = $statusInfoModel->getStatus() === $status;
            $statusInfo[$status->getId()] = [
                'name' => $status->getName(),
                'statusDate' => $statusInfoModel->getStatusDate($status),
                'isCurrent' => $isCurrent,
                'setAutomatically' => true,
                'isActive' => $statusInfoModel->isStatusActive($status),
            ];
        }
        return $statusInfo;
    }

    /**
     * Returns the status info for the given implementation project service
     *
     * @param ImplementationProjectService $projectService
     * @return array
     */
    public function getProjectServiceStatusInfo(ImplementationProjectService $projectService): array
    {
        $statusChoices = $this->getStatusChoices();
        $statusInfo = [];
        $serviceStatus = $projectService->getStatus();
        if (null === $serviceStatus && null !== $project = $projectService->getImplementationProject()) {
            $serviceStatus = $project->getStatus();
        }
        $hasCustomState = false;
        if (null !== $serviceStatus && !$serviceStatus->isSetAutomatically()) {
            $hasCustomState = true;
            $statusInfo[$serviceStatus->getId()] = [
                'name' => $serviceStatus->getName(),
                'statusDate' => null,
                'isCurrent' => true,
                'setAutomatically' => false,
                'isActive' => true,
            ];
        }
        foreach ($statusChoices as $status) {
            /** @var ImplementationStatus $status */
            $isCurrent = !$hasCustomState && $serviceStatus === $status;
            $statusInfo[$status->getId()] = [
                'name' => $status->getName(),
                'statusDate' => null,
                'isCurrent' => $isCurrent,
                'setAutomatically' => true,
                'isActive' => !$hasCustomState && (null !== $serviceStatus && $status->getLevel() <= $serviceStatus->getLevel()),
            ];
        }
        return $statusInfo;
    }

    /**
     * Updates the status for all implementation projects
     *
     * @param int $forceUpdateStatusId Force recheck of current status if project currently has this status
     * @param array|null $projectIdList
     * @return int The number of updated rows
     */
    public function setCurrentStatusForAll(int $forceUpdateStatusId, ?array $projectIdList = null): int
    {
        $updatedRowCount = 0;
        $em = $this->registry->getManager();
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository(ImplementationProject::class);
        $initialStatusEntity = $em->find(ImplementationStatus::class, ImplementationStatus::STATUS_ID_PREPARED);
        if (!empty($projectIdList)) {
            $queryBuilder = $repository->createQueryBuilder('p');
            $queryBuilder->where(
                $queryBuilder->expr()->in('p', $projectIdList)
            );
            $result = $queryBuilder->getQuery()->execute();
        } else {
            $result = $repository->findAll();
        }
        foreach ($result as $project) {
            /** @var ImplementationProject $project */
            $status = $project->getStatus();
            if ($initialStatusEntity && (null === $status || $status->getId() === $forceUpdateStatusId)) {
                $newStatus = $project->determineNewStatus($initialStatusEntity);
            } else {
                // Test with previous status, so assignments get updated automatically, if the date changes
                if ($status->isSetAutomatically() && null !== $prevStatus = $status->getPrevStatus()) {
                    $newStatus = $project->determineNewStatus($prevStatus);
                } else {
                    $newStatus = $project->determineNewStatus($status);
                }
            }
            if (null !== $newStatus && $newStatus !== $status) {
                $project->setStatus($newStatus);
                ++$updatedRowCount;
            }
        }
        $em->flush();
        return $updatedRowCount;
    }

}