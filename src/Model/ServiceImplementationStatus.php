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

namespace App\Model;

use App\Entity\ImplementationProjectService;
use App\Entity\ImplementationStatus;
use App\Entity\Service;
use DateTime;

/**
 * Class ServiceImplementationStatus
 * Process an entity reference property to find problems when deleting an entity
 *
 * @package App\Model
 */
class ServiceImplementationStatus implements ImplementationStatusInfoInterface
{
    use ImplementationStatusInfoTrait;

    /**
     * ServiceImplementationStatus constructor.
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $implementationProjects = $service->getImplementationProjects();
        foreach ($implementationProjects as $implementationProjectService) {
            $this->setDataFromProjectService($implementationProjectService);
        }
    }

    /**
     * Status
     * @var ImplementationStatus|null
     */
    private $status;

    /**
     * @var null|DateTime
     */
    protected $projectStartAt;

    /**
     * @var null|DateTime
     */
    protected $conceptStatusAt;

    /**
     * @var null|DateTime
     */
    protected $implementationStatusAt;

    /**
     * @var null|DateTime
     */
    protected $commissioningStatusAt;

    /**
     * @var null|DateTime
     */
    protected $pilotingStatusAt;

    /**
     * @var null|DateTime
     */
    protected $nationwideRolloutAt;


    /**
     * @return ImplementationStatus|null
     */
    public function getStatus(): ?ImplementationStatus
    {
        return $this->status;
    }

    /**
     * @param ImplementationStatus|null $status
     */
    public function setStatus(?ImplementationStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime|null
     */
    public function getProjectStartAt(): ?DateTime
    {
        return $this->projectStartAt;
    }

    /**
     * @param DateTime|null $projectStartAt
     */
    public function setProjectStartAt(?DateTime $projectStartAt): void
    {
        $this->projectStartAt = $projectStartAt;
    }

    /**
     * @return DateTime|null
     */
    public function getConceptStatusAt(): ?DateTime
    {
        return $this->conceptStatusAt;
    }

    /**
     * @param DateTime|null $conceptStatusAt
     */
    public function setConceptStatusAt(?DateTime $conceptStatusAt): void
    {
        $this->conceptStatusAt = $conceptStatusAt;
    }

    /**
     * @return DateTime|null
     */
    public function getImplementationStatusAt(): ?DateTime
    {
        return $this->implementationStatusAt;
    }

    /**
     * @param DateTime|null $implementationStatusAt
     */
    public function setImplementationStatusAt(?DateTime $implementationStatusAt): void
    {
        $this->implementationStatusAt = $implementationStatusAt;
    }

    /**
     * @return DateTime|null
     */
    public function getCommissioningStatusAt(): ?DateTime
    {
        return $this->commissioningStatusAt;
    }

    /**
     * @param DateTime|null $commissioningStatusAt
     */
    public function setCommissioningStatusAt(?DateTime $commissioningStatusAt): void
    {
        $this->commissioningStatusAt = $commissioningStatusAt;
    }

    /**
     * @return DateTime|null
     */
    public function getPilotingStatusAt(): ?DateTime
    {
        return $this->pilotingStatusAt;
    }

    /**
     * @param DateTime|null $pilotingStatusAt
     */
    public function setPilotingStatusAt(?DateTime $pilotingStatusAt): void
    {
        $this->pilotingStatusAt = $pilotingStatusAt;
    }

    /**
     * @return DateTime|null
     */
    public function getNationwideRolloutAt(): ?DateTime
    {
        return $this->nationwideRolloutAt;
    }

    /**
     * @param DateTime|null $nationwideRolloutAt
     */
    public function setNationwideRolloutAt(?DateTime $nationwideRolloutAt): void
    {
        $this->nationwideRolloutAt = $nationwideRolloutAt;
    }

    /**
     * Set the model data from the given project
     *
     * @param ImplementationProjectService $implementationProjectService
     */
    protected function setDataFromProjectService(ImplementationProjectService $implementationProjectService): void
    {
        $projectServiceStatus = $implementationProjectService->getStatus();
        if (null !== $projectServiceStatus && (null === $this->getStatus()
            || ($projectServiceStatus->getId() > $this->getStatus()->getId()
                && $projectServiceStatus->getId() !== ImplementationStatus::STATUS_ID_DEFERRED))) {
            $this->setStatus($projectServiceStatus);
        }
        $project = $implementationProjectService->getImplementationProject();
        if (null === $project) {
            return;
        }
        if (null === $projectServiceStatus) {
            $status = $project->getStatus();
            if (null !== $status && (null === $this->getStatus()
                || ($status->getId() > $this->getStatus()->getId()
                    && $status->getId() !== ImplementationStatus::STATUS_ID_DEFERRED))) {
                $this->setStatus($status);
            }
        }
        $projectStartAt = $project->getProjectStartAt();
        if (null !== $projectStartAt
            && (null === $this->getProjectStartAt() || $projectStartAt < $this->getProjectStartAt())) {
            $this->setProjectStartAt($projectStartAt);
        }
        $conceptStatusAt = $project->getConceptStatusAt();
        if (null !== $conceptStatusAt
            && (null === $this->getConceptStatusAt() || $conceptStatusAt < $this->getConceptStatusAt())) {
            $this->setConceptStatusAt($conceptStatusAt);
        }
        $implementationStatusAt = $project->getImplementationStatusAt();
        if (null !== $implementationStatusAt
            && (null === $this->getImplementationStatusAt() || $implementationStatusAt < $this->getImplementationStatusAt())) {
            $this->setImplementationStatusAt($implementationStatusAt);
        }
        $pilotingStatusAt = $project->getPilotingStatusAt();
        if (null !== $pilotingStatusAt
            && (null === $this->getPilotingStatusAt() || $pilotingStatusAt < $this->getPilotingStatusAt())) {
            $this->setPilotingStatusAt($pilotingStatusAt);
        }
        $commissioningStatusAt = $project->getCommissioningStatusAt();
        if (null !== $commissioningStatusAt
            && (null === $this->getCommissioningStatusAt() || $commissioningStatusAt < $this->getCommissioningStatusAt())) {
            $this->setCommissioningStatusAt($commissioningStatusAt);
        }
        $nationwideRolloutAt = $project->getNationwideRolloutAt();
        if (null !== $nationwideRolloutAt
            && (null === $this->getNationwideRolloutAt() || $nationwideRolloutAt < $this->getNationwideRolloutAt())) {
            $this->setNationwideRolloutAt($nationwideRolloutAt);
        }
    }
}
