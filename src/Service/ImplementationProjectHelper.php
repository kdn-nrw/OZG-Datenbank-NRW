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
     * @param ImplementationProject $project
     * @return array
     */
    public function getProjectStatusInfo(ImplementationProject $project): array
    {
        $statusChoices = $this->getStatusChoices();
        $statusInfo = [];
        foreach ($statusChoices as $status) {
            /** @var ImplementationStatus $status */
            $isCurrent = $project->getStatus() === $status;
            $statusInfo[$status->getId()] = [
                'name' => $status->getName(),
                'statusDate' => $project->getStatusDate($status),
                'isCurrent' => $isCurrent,
                'setAutomatically' => true,
                'isActive' => $project->isStatusActive($status),
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
        if (null !== $serviceStatus && !$serviceStatus->isSetAutomatically()) {
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
            $isCurrent = $serviceStatus === $status;
            $statusInfo[$status->getId()] = [
                'name' => $status->getName(),
                'statusDate' => null,
                'isCurrent' => $isCurrent,
                'setAutomatically' => true,
                'isActive' => null !== $serviceStatus && $status->getLevel() <= $serviceStatus->getLevel(),
            ];
        }
        return $statusInfo;
    }

    /**
     * Updates the status for all implementation projects
     *
     * @return void
     */
    public function setCurrentStatusForAll(): void
    {
        $em = $this->registry->getManager();
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository(ImplementationProject::class);
        $result = $repository->findAll();
        foreach ($result as $project) {
            $project->updateStatus();
        }
        $em->flush();
    }

}