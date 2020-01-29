<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Base\SoftdeletableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class OZG service systems
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service_system")
 * @ORM\HasLifecycleCallbacks
 */
class ServiceSystem extends BaseNamedEntity
{

    /**
     * ozggid
     * @var string|null
     *
     * @ORM\Column(type="string", name="service_key", length=255, nullable=true)
     */
    private $serviceKey;

    /**
     * Status
     * @var Status|null
     *
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    // name => ozglstg
    /*
    ozgschl
    ozgtyp
     */
    /**
     * Contact
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $contact;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var Situation|null
     * @ORM\ManyToOne(targetEntity="Situation", inversedBy="services", cascade={"persist"})
     */
    private $situation;

    /**
     * @var Priority|null
     * @ORM\ManyToOne(targetEntity="Priority", inversedBy="serviceSystems")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $priority;

    /**
     * @var Service[]|Collection
     * @ORM\OneToMany(targetEntity="Service", mappedBy="serviceSystem", cascade={"persist"})
     */
    private $services;

    /**
     * @var Jurisdiction[]|Collection
     * @ORM\ManyToMany(targetEntity="Jurisdiction", inversedBy="serviceSystems")
     * @ORM\JoinTable(name="ozg_service_system_jurisdiction",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="jurisdiction_id", referencedColumnName="id")
     *   }
     * )
     */
    private $jurisdictions;

    /**
     * @var ImplementationProject[]|Collection
     * @ORM\ManyToMany(targetEntity="ImplementationProject", mappedBy="serviceSystems")
     */
    private $implementationProjects;

    /**
     * @var Laboratory[]|Collection
     * @ORM\ManyToMany(targetEntity="Laboratory", mappedBy="serviceSystems")
     */
    private $laboratories;

    /**
     * @var MinistryState[]|Collection
     * @ORM\ManyToMany(targetEntity="MinistryState", mappedBy="serviceSystems")
     */
    private $stateMinistries;

    /**
     * @var Bureau[]|Collection
     * @ORM\ManyToMany(targetEntity="Bureau", mappedBy="serviceSystems")
     */
    private $bureaus;

    public function __construct()
    {
        $this->bureaus = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->jurisdictions = new ArrayCollection();
        $this->implementationProjects = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
        $this->stateMinistries = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getServiceKey(): ?string
    {
        return $this->serviceKey;
    }

    /**
     * @param string|null $serviceKey
     */
    public function setServiceKey(?string $serviceKey): void
    {
        $this->serviceKey = $serviceKey;
    }

    /**
     * @return string|null
     */
    public function getContact(): ?string
    {
        return $this->contact;
    }

    /**
     * @param string|null $contact
     */
    public function setContact(?string $contact): void
    {
        $this->contact = $contact;
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
     * @return Situation|null
     */
    public function getSituation(): ?Situation
    {
        return $this->situation;
    }

    /**
     * @param Situation $situation
     */
    public function setSituation($situation): void
    {
        $this->situation = $situation;
    }

    /**
     * @return Status|null
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * @param Status|null $status
     */
    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Priority|null
     */
    public function getPriority(): ?Priority
    {
        return $this->priority;
    }

    /**
     * @param Priority|null $priority
     */
    public function setPriority(?Priority $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @param Bureau $bureau
     * @return self
     */
    public function addBureau($bureau): self
    {
        if (!$this->bureaus->contains($bureau)) {
            $this->bureaus->add($bureau);
            $bureau->addServiceSystem($this);
        }

        return $this;
    }

    /**
     * @param Bureau $bureau
     * @return self
     */
    public function removeBureau($bureau): self
    {
        if ($this->bureaus->contains($bureau)) {
            $this->bureaus->removeElement($bureau);
            $bureau->removeServiceSystem($this);
        }

        return $this;
    }

    /**
     * @return Bureau[]|Collection
     */
    public function getBureaus()
    {
        return $this->bureaus;
    }

    /**
     * @param Bureau[]|Collection $bureaus
     */
    public function setBureaus($bureaus): void
    {
        $this->bureaus = $bureaus;
    }

    /**
     * @param Service $service
     * @return self
     */
    public function addService($service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setServiceSystem($this);
        }

        return $this;
    }

    /**
     * @param Service $service
     * @return self
     */
    public function removeService($service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            if ($service instanceof SoftdeletableEntityInterface) {
                $service->setDeletedAt(new \DateTime());
            }
        }

        return $this;
    }

    /**
     * @return Service[]|Collection
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    /**
     * @param Service[]|Collection $services
     */
    public function setServices(Collection $services): void
    {
        $this->services = $services;
    }

    /**
     * @param Jurisdiction $jurisdiction
     * @return self
     */
    public function addJurisdiction($jurisdiction): self
    {
        if (!$this->jurisdictions->contains($jurisdiction)) {
            $this->jurisdictions->add($jurisdiction);
            $jurisdiction->addServiceSystem($this);
        }

        return $this;
    }

    /**
     * @param Jurisdiction $jurisdiction
     * @return self
     */
    public function removeJurisdiction($jurisdiction): self
    {
        if ($this->jurisdictions->contains($jurisdiction)) {
            $this->jurisdictions->removeElement($jurisdiction);
            $jurisdiction->removeServiceSystem($this);
        }

        return $this;
    }

    /**
     * @return Jurisdiction[]|Collection
     */
    public function getJurisdictions()
    {
        return $this->jurisdictions;
    }

    /**
     * @param Jurisdiction[]|Collection $jurisdictions
     */
    public function setJurisdictions($jurisdictions): void
    {
        $this->jurisdictions = $jurisdictions;
    }

    /**
     * Returns true if the state ministries property is active
     *
     * @return bool
     */
    public function getStateMinistriesEnabled(): bool
    {
        $jurisdictions = $this->getJurisdictions();
        foreach ($jurisdictions as $jurisdiction) {
            if (in_array($jurisdiction->getId(), [Jurisdiction::TYPE_STATE, Jurisdiction::TYPE_COMMUNE], false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ImplementationProject $implementationProject
     * @return self
     */
    public function addImplementationProject($implementationProject): self
    {
        if (!$this->implementationProjects->contains($implementationProject)) {
            $this->implementationProjects->add($implementationProject);
            $implementationProject->addServiceSystem($this);
        }

        return $this;
    }

    /**
     * @param ImplementationProject $implementationProject
     * @return self
     */
    public function removeImplementationProject($implementationProject): self
    {
        if ($this->implementationProjects->contains($implementationProject)) {
            $this->implementationProjects->removeElement($implementationProject);
            $implementationProject->removeServiceSystem($this);
        }

        return $this;
    }

    /**
     * @return ImplementationProject[]|Collection
     */
    public function getImplementationProjects()
    {
        return $this->implementationProjects;
    }

    /**
     * @param ImplementationProject[]|Collection $implementationProjects
     */
    public function setImplementationProjects($implementationProjects): void
    {
        $this->implementationProjects = $implementationProjects;
    }

    /**
     * @param Laboratory $laboratory
     * @return self
     */
    public function addLaboratory($laboratory): self
    {
        if (!$this->laboratories->contains($laboratory)) {
            $this->laboratories->add($laboratory);
            $laboratory->addServiceSystem($this);
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
            $laboratory->removeServiceSystem($this);
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
     * @param MinistryState $stateMinistry
     * @return self
     */
    public function addStateMinistry($stateMinistry): self
    {
        if (!$this->stateMinistries->contains($stateMinistry)) {
            $this->stateMinistries->add($stateMinistry);
            $stateMinistry->addServiceSystem($this);
        }

        return $this;
    }

    /**
     * @param MinistryState $stateMinistry
     * @return self
     */
    public function removeStateMinistry($stateMinistry): self
    {
        if ($this->stateMinistries->contains($stateMinistry)) {
            $this->stateMinistries->removeElement($stateMinistry);
            $stateMinistry->removeServiceSystem($this);
        }

        return $this;
    }

    /**
     * @return MinistryState[]|Collection
     */
    public function getStateMinistries()
    {
        return $this->stateMinistries;
    }

    /**
     * @param MinistryState[]|Collection $stateMinistries
     */
    public function setStateMinistries($stateMinistries): void
    {
        $this->stateMinistries = $stateMinistries;
    }
}
