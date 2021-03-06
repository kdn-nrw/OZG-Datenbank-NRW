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

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Base\SluggableEntityTrait;
use App\Entity\Base\SluggableInterface;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class implementation project
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_implementation_project")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
 */
class ImplementationProject extends BaseNamedEntity implements SluggableInterface, HasMetaDateEntityInterface, HasSolutionsEntityInterface
{
    use SluggableEntityTrait;

    /**
     * Status
     * @var ImplementationStatus|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ImplementationStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="project_start_at")
     */
    protected $projectStartAt;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="concept_status_at")
     */
    protected $conceptStatusAt;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="implementation_status_at")
     */
    protected $implementationStatusAt;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="commissioning_status_at")
     */
    protected $commissioningStatusAt;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="nationwide_rollout_at")
     */
    protected $nationwideRolloutAt;

    /**
     * Notes
     *
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes = '';

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\ServiceSystem", inversedBy="implementationProjects")
     * @ORM\JoinTable(name="ozg_implementation_project_service_system",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   }
     * )
     */
    private $serviceSystems;

    /**
     * @var ImplementationProjectService[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\ImplementationProjectService", mappedBy="implementationProject", cascade={"all"}, orphanRemoval=true)
     */
    private $services;

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Solution", inversedBy="implementationProjects")
     * @ORM\JoinTable(name="ozg_implementation_project_solution",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   }
     * )
     */
    private $solutions;

    /**
     * @var Contact[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Contact", inversedBy="implementationProjects")
     * @ORM\JoinTable(name="ozg_implementation_project_contact",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *   }
     * )
     */
    private $contacts;

    /**
     * @var Organisation[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Organisation")
     * @ORM\JoinTable(name="ozg_implementation_project_organisation_interested",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $interestedOrganisations;

    /**
     * @var Organisation[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Organisation")
     * @ORM\JoinTable(name="ozg_implementation_project_organisation_participation",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $participationOrganisations;

    /**
     * @var Laboratory[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Laboratory")
     * @ORM\JoinTable(name="ozg_implementation_project_laboratory",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="laboratory_id", referencedColumnName="id")
     *   }
     * )
     */
    private $laboratories;

    /**
     * @var Organisation[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Organisation")
     * @ORM\JoinTable(name="ozg_implementation_project_organisation_leaders",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     *   }
     * )
     */
    private $projectLeaders;

    /**
     * @var Funding[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Funding", inversedBy="implementationProjects")
     * @ORM\JoinTable(name="ozg_implementation_project_funding",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="funding_id", referencedColumnName="id")
     *   }
     * )
     */
    private $fundings;

    /**
     * @var Contact[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Contact")
     * @ORM\JoinTable(name="ozg_implementation_project_fim_export",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *   }
     * )
     */
    private $fimExperts;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->fimExperts = new ArrayCollection();
        $this->fundings = new ArrayCollection();
        $this->interestedOrganisations = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
        $this->participationOrganisations = new ArrayCollection();
        $this->projectLeaders = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->serviceSystems = new ArrayCollection();
        $this->solutions = new ArrayCollection();
    }

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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
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
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function addServiceSystem($serviceSystem): self
    {
        if (!$this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->add($serviceSystem);
            $serviceSystem->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function removeServiceSystem($serviceSystem): self
    {
        if ($this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->removeElement($serviceSystem);
            $serviceSystem->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return ServiceSystem[]|Collection
     */
    public function getServiceSystems()
    {
        return $this->serviceSystems;
    }

    /**
     * @param ServiceSystem[]|Collection $serviceSystems
     */
    public function setServiceSystems($serviceSystems): void
    {
        $this->serviceSystems = $serviceSystems;
    }

    /**
     * @param ImplementationProjectService $service
     * @return self
     */
    public function addService(ImplementationProjectService $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setImplementationProject($this);
            if (null !== $serviceRef = $service->getService()) {
                $serviceRef->addImplementationProject($service);
            }
        }

        return $this;
    }

    /**
     * @param ImplementationProjectService $service
     * @return self
     */
    public function removeService(ImplementationProjectService $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            if (null !== $serviceRef = $service->getService()) {
                $serviceRef->removeImplementationProject($service);
            }
        }

        return $this;
    }

    /**
     * @return ImplementationProjectService[]|Collection
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    /**
     * @param ImplementationProjectService[]|Collection $services
     */
    public function setServices(Collection $services): void
    {
        $this->services = $services;
    }

    /**
     * Returns the subjects of the referenced service systems
     *
     * @return Subject[]
     */
    public function getSubjects(): array
    {
        $subjects = [];
        $serviceSystems = $this->getServiceSystems();
        foreach ($serviceSystems as $serviceSystem) {
            if (null !== $serviceSystem->getSituation()
                && null !== $subject = $serviceSystem->getSituation()->getSubject()) {
                if (!isset($subjects[$subject->getId()])) {
                    $subjects[$subject->getId()] = $subject;
                }
            }
        }
        return $subjects;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function removeSolution($solution): self
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            $solution->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return Solution[]|Collection
     */
    public function getPublishedSolutions()
    {
        $publishedSolutions = new ArrayCollection();
        foreach ($this->solutions as $solution) {
            if ($solution->isPublished()) {
                $publishedSolutions->add($solution);
            }
        }
        return $publishedSolutions;
    }

    /**
     * @return Solution[]|Collection
     */
    public function getSolutions(): Collection
    {
        return $this->solutions;
    }

    /**
     * @param Solution[]|Collection $solutions
     */
    public function setSolutions($solutions): void
    {
        $this->solutions = $solutions;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function addContact($contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function removeContact($contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            $contact->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param Contact[]|Collection $contacts
     */
    public function setContacts($contacts): void
    {
        $this->contacts = $contacts;
    }

    /**
     * @param Organisation $interestedOrganisation
     * @return self
     */
    public function addInterestedOrganisation($interestedOrganisation): self
    {
        if (!$this->interestedOrganisations->contains($interestedOrganisation)) {
            $this->interestedOrganisations->add($interestedOrganisation);
            //$interestedOrganisation->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param Organisation $interestedOrganisation
     * @return self
     */
    public function removeInterestedOrganisation($interestedOrganisation): self
    {
        if ($this->interestedOrganisations->contains($interestedOrganisation)) {
            $this->interestedOrganisations->removeElement($interestedOrganisation);
            //$interestedOrganisation->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return Organisation[]|Collection
     */
    public function getInterestedOrganisations()
    {
        return $this->interestedOrganisations;
    }

    /**
     * @param Organisation[]|Collection $interestedOrganisations
     */
    public function setInterestedOrganisations($interestedOrganisations): void
    {
        $this->interestedOrganisations = $interestedOrganisations;
    }

    /**
     * @param Organisation $participationOrganisation
     * @return self
     */
    public function addParticipationOrganisation($participationOrganisation): self
    {
        if (!$this->participationOrganisations->contains($participationOrganisation)) {
            $this->participationOrganisations->add($participationOrganisation);
            //$participationOrganisation->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param Organisation $participationOrganisation
     * @return self
     */
    public function removeParticipationOrganisation($participationOrganisation): self
    {
        if ($this->participationOrganisations->contains($participationOrganisation)) {
            $this->participationOrganisations->removeElement($participationOrganisation);
            //$participationOrganisation->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return Organisation[]|Collection
     */
    public function getParticipationOrganisations()
    {
        return $this->participationOrganisations;
    }

    /**
     * @param Organisation[]|Collection $participationOrganisations
     */
    public function setParticipationOrganisations($participationOrganisations): void
    {
        $this->participationOrganisations = $participationOrganisations;
    }

    /**
     * @param Laboratory $laboratory
     * @return self
     */
    public function addLaboratory($laboratory): self
    {
        if (!$this->laboratories->contains($laboratory)) {
            $this->laboratories->add($laboratory);
            //$laboratory->addService($this);
        }

        return $this;
    }

    /**
     * @param Laboratory $laboratory
     * @return self
     */
    public function removeLaboratory($laboratory): self
    {
        if ($this->laboratories->contains($laboratory)) {
            $this->laboratories->removeElement($laboratory);
            //$laboratory->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return Laboratory[]|Collection
     */
    public function getLaboratories()
    {
        return $this->laboratories;
    }

    /**
     * @param Laboratory[]|Collection $laboratories
     */
    public function setLaboratories($laboratories): void
    {
        $this->laboratories = $laboratories;
    }

    /**
     * @param Organisation $projectLeader
     * @return self
     */
    public function addProjectLeader($projectLeader): self
    {
        if (!$this->projectLeaders->contains($projectLeader)) {
            $this->projectLeaders->add($projectLeader);
            //$projectLeader->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param Organisation $projectLeader
     * @return self
     */
    public function removeProjectLeader($projectLeader): self
    {
        if ($this->projectLeaders->contains($projectLeader)) {
            $this->projectLeaders->removeElement($projectLeader);
            //$projectLeader->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return Organisation[]|Collection
     */
    public function getProjectLeaders()
    {
        return $this->projectLeaders;
    }

    /**
     * @param Organisation[]|Collection $projectLeaders
     */
    public function setProjectLeaders($projectLeaders): void
    {
        $this->projectLeaders = $projectLeaders;
    }

    /**
     * @param Funding $funding
     * @return self
     */
    public function addFunding($funding): self
    {
        if (!$this->fundings->contains($funding)) {
            $this->fundings->add($funding);
            $funding->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param Funding $funding
     * @return self
     */
    public function removeFunding($funding): self
    {
        if ($this->fundings->contains($funding)) {
            $this->fundings->removeElement($funding);
            $funding->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return Funding[]|Collection
     */
    public function getFundings()
    {
        return $this->fundings;
    }

    /**
     * @param Funding[]|Collection $fundings
     */
    public function setFundings($fundings): void
    {
        $this->fundings = $fundings;
    }

    /**
     * Returns the distinct bureaus assigned to the referenced services
     *
     * @return array
     */
    public function getBureaus(): array
    {
        $distinctEntities = [];
        $services = $this->getServices();
        foreach ($services as $projectService) {
            if (null !== $service = $projectService->getService()) {
                $serviceBureaus = $service->getBureaus();
                foreach ($serviceBureaus as $entity) {
                    if (!isset($distinctEntities[$entity->getId()])) {
                        $distinctEntities[$entity->getId()] = $entity;
                    }
                }
            }
        }
        return $distinctEntities;
    }

    /**
     * Returns the distinct portals assigned to the referenced services
     *
     * @return array
     */
    public function getPortals(): array
    {
        $distinctEntities = [];
        $services = $this->getServices();
        foreach ($services as $projectService) {
            if (null !== $service = $projectService->getService()) {
                $servicePortals = $service->getPortals();
                foreach ($servicePortals as $entity) {
                    if (!isset($distinctEntities[$entity->getId()])) {
                        $distinctEntities[$entity->getId()] = $entity;
                    }
                }
            }
        }
        return $distinctEntities;
    }

    /**
     * @param Contact $fimExpert
     * @return self
     */
    public function addFimExpert(Contact $fimExpert): self
    {
        if (!$this->fimExperts->contains($fimExpert)) {
            $this->fimExperts->add($fimExpert);
        }

        return $this;
    }

    /**
     * @param Contact $fimExpert
     * @return self
     */
    public function removeFimExpert(Contact $fimExpert): self
    {
        if ($this->fimExperts->contains($fimExpert)) {
            $this->fimExperts->removeElement($fimExpert);
        }

        return $this;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getFimExperts()
    {
        return $this->fimExperts;
    }

    /**
     * @param Contact[]|Collection $fimExperts
     */
    public function setFimExperts($fimExperts): void
    {
        $this->fimExperts = $fimExperts;
    }

    /**
     * Hook on persist and update operations.
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist(): void
    {
        $this->updateStatus();
    }

    /**
     * Returns the true if the given status is active
     *
     * @param ImplementationStatus $status
     * @return bool
     */
    public function isStatusActive(ImplementationStatus $status): bool
    {
        $isActive = $status === $this->getStatus();
        if (!$isActive && null !== $statusId = $status->getId()) {
            switch ($statusId) {
                case ImplementationStatus::STATUS_ID_PREPARED:
                    $isActive = null !== $this->projectStartAt && $this->projectStartAt->getTimestamp() <= time();
                    break;
                case ImplementationStatus::STATUS_ID_CONCEPT:
                    $isActive = null !== $this->conceptStatusAt && $this->conceptStatusAt->getTimestamp() <= time();
                    break;
                case ImplementationStatus::STATUS_ID_IMPLEMENTATION:
                    $isActive = null !== $this->implementationStatusAt && $this->implementationStatusAt->getTimestamp() <= time();
                    break;
                case ImplementationStatus::STATUS_ID_COMMISSIONING:
                    $isActive = null !== $this->commissioningStatusAt && $this->commissioningStatusAt->getTimestamp() <= time();
                    break;
                case ImplementationStatus::STATUS_ID_NATIONWIDE_ROLLOUT:
                    $isActive = null !== $this->nationwideRolloutAt && $this->nationwideRolloutAt->getTimestamp() <= time();
                    break;
            }
        }
        return $isActive;
    }

    /**
     * Returns the status date for the given status
     *
     * @param ImplementationStatus $status
     * @return DateTime|null
     */
    public function getStatusDate(ImplementationStatus $status): ?DateTime
    {
        $statusDate = null;
        if (null !== $status && null !== $statusId = $status->getId()) {
            switch ($statusId) {
                case ImplementationStatus::STATUS_ID_PREPARED:
                    $statusDate = $this->projectStartAt;
                    break;
                case ImplementationStatus::STATUS_ID_CONCEPT:
                    $statusDate = $this->conceptStatusAt;
                    break;
                case ImplementationStatus::STATUS_ID_IMPLEMENTATION:
                    $statusDate = $this->implementationStatusAt;
                    break;
                case ImplementationStatus::STATUS_ID_COMMISSIONING:
                    $statusDate = $this->commissioningStatusAt;
                    break;
                case ImplementationStatus::STATUS_ID_NATIONWIDE_ROLLOUT:
                    $statusDate = $this->nationwideRolloutAt;
                    break;
            }
        }
        return $statusDate;
    }

    /**
     * Updates the project status based on the date fields set in the project
     * @param int $callCount
     */
    public function updateStatus($callCount = 1): void
    {
        $status = $this->getStatus();
        if ($callCount < 10 && null !== $status && null !== $switchType = $status->getStatusSwitch()) {
            switch ($switchType) {
                case ImplementationStatus::STATUS_SWITCH_PREPARED:
                    $newStatus = $this->getNewStatusBasedOnDateTime($status, null, $this->projectStartAt);
                    break;
                case ImplementationStatus::STATUS_SWITCH_CONCEPT:
                    $newStatus = $this->getNewStatusBasedOnDateTime($status, $this->projectStartAt, $this->conceptStatusAt);
                    break;
                case ImplementationStatus::STATUS_SWITCH_IMPLEMENTATION:
                    $newStatus = $this->getNewStatusBasedOnDateTime($status, $this->conceptStatusAt, $this->implementationStatusAt);
                    break;
                case ImplementationStatus::STATUS_SWITCH_COMMISSIONING:
                    $newStatus = $this->getNewStatusBasedOnDateTime($status, $this->implementationStatusAt, $this->commissioningStatusAt);
                    break;
                case ImplementationStatus::STATUS_SWITCH_NATIONWIDE_ROLLOUT:
                    $newStatus = $this->getNewStatusBasedOnDateTime($status, $this->commissioningStatusAt, $this->nationwideRolloutAt);
                    break;
                default:
                    $newStatus = null;
                    break;
            }
            if (null !== $newStatus && $newStatus !== $status) {
                $this->setStatus($newStatus);
                $this->updateStatus($callCount + 1);
            }
        }
    }

    /**
     * Returns the previous or next status depending on the given dates
     * If the previous date is null or the is in the future the previous status is returned
     * If the next date is not null or the is in the past the previous status is returned
     *
     * @param ImplementationStatus $status
     * @param DateTime|null $prevDateTime
     * @param DateTime|null $nextDateTime
     * @return ImplementationStatus|null
     */
    private function getNewStatusBasedOnDateTime(
        ImplementationStatus $status,
        ?DateTime $prevDateTime,
        ?DateTime $nextDateTime
    ): ?ImplementationStatus {
        $newStatus = null;
        if (null !== $nextDateTime && $nextDateTime->getTimestamp() <= time())  {
            $newStatus = $status->getNextStatus();
        } elseif (null === $prevDateTime || $prevDateTime->getTimestamp() > time()) {
            $newStatus = $status->getPrevStatus();
        }
        return $newStatus;
    }
}
