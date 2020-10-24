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

use App\Entity\ImplementationProject;
use App\Entity\ImplementationStatus;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ImplementationProjectHelper
{
    /**
     * @var \Doctrine\Persistence\ManagerRegistry|ManagerRegistry
     */
    protected $registry;

    /**
     * @required
     * @param ManagerRegistry $registry
     */
    public function injectManagerRegistry(ManagerRegistry $registry): void
    {
        $this->registry = $registry;
    }

    /**
     * Updates the status for all implementation projects
     *
     * @param ImplementationProject $project
     * @return array
     */
    public function getProjectStatusInfo(ImplementationProject $project): array
    {
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository(ImplementationStatus::class);
        $result = $repository->findBy(['setAutomatically' => 1], ['level' => 'ASC']);
        $statusInfo = [];
        $now = time();
        foreach ($result as $status) {
            /** @var ImplementationStatus $status */
            $isCurrent = $project->getStatus() === $status;
            $statusInfo[$status->getId()] = [
                'name' => $status->getName(),
                'statusDate' => $project->getStatusDate($status),
                'isCurrent' => $isCurrent,
                'isActive' => $project->isStatusActive($status),
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