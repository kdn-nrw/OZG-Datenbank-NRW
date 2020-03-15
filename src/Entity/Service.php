<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Leistungen (LeiKA)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service")
 * @ORM\HasLifecycleCallbacks
 */
class Service extends AbstractService
{

    /**
     * LeiKa-Typ
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serviceType;

    /**
     * Rechtsgrundlage(n)
     * @var string|null
     *
     * @ORM\Column(name="legal_basis", type="text", nullable=true)
     */
    private $legalBasis = '';

    /**
     * Gesetz(e)
     * @var string|null
     *
     * @ORM\Column(name="laws", type="text", nullable=true)
     */
    private $laws = '';

    /**
     * Gesetzeskürzel
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lawShortcuts;
    /**
     * SDG1-Relevanz
     * @var bool
     *
     * @ORM\Column(name="relevance1", type="boolean")
     */
    protected $relevance1 = false;
    /**
     * SDG2-Relevanz
     * @var bool
     *
     * @ORM\Column(name="relevance2", type="boolean")
     */
    protected $relevance2 = false;

    /**
     * Status
     * @var Status|null
     *
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * @var ServiceSystem|null
     * @ORM\ManyToOne(targetEntity="ServiceSystem", inversedBy="services", cascade={"persist"})
     * @ORM\JoinColumn(name="service_system_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $serviceSystem;

    /**
     * @var ServiceSolution[]|Collection
     * @ORM\OneToMany(targetEntity="ServiceSolution", mappedBy="service", cascade={"all"}, orphanRemoval=true)
     */
    private $serviceSolutions;

    /**
     * @var Laboratory[]|Collection
     * @ORM\ManyToMany(targetEntity="Laboratory", mappedBy="services")
     */
    private $laboratories;

    /**
     * @var Jurisdiction[]|Collection
     * @ORM\ManyToMany(targetEntity="Jurisdiction", inversedBy="services")
     * @ORM\JoinTable(name="ozg_service_jurisdiction",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="jurisdiction_id", referencedColumnName="id")
     *   }
     * )
     */
    private $jurisdictions;

    /**
     * Toggle inheritance of jurisdictions from service system
     * @var bool
     *
     * @ORM\Column(name="inherit_jurisdictions", type="boolean")
     */
    protected $inheritJurisdictions = true;

    /**
     * @var MinistryState[]|Collection
     * @ORM\ManyToMany(targetEntity="MinistryState")
     * @ORM\JoinTable(name="ozg_service_ministry_state",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ministry_state_id", referencedColumnName="id")
     *   }
     * )
     */
    private $stateMinistries;

    /**
     * Toggle inheritance of bureaus from service system
     * @var bool
     *
     * @ORM\Column(name="inherit_state_ministries", type="boolean")
     */
    protected $inheritStateMinistries = true;

    /**
     * @var Bureau[]|Collection
     * @ORM\ManyToMany(targetEntity="Bureau", mappedBy="services")
     */
    private $bureaus;

    /**
     * Toggle inheritance of bureaus from service system
     * @var bool
     *
     * @ORM\Column(name="inherit_bureaus", type="boolean")
     */
    protected $inheritBureaus = true;

    /**
     * Toggle inheritance of rule authorities from service system
     * @var bool
     *
     * @ORM\Column(name="inherit_rule_authorities", type="boolean")
     */
    protected $inheritRuleAuthorities = true;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="SpecializedProcedure", inversedBy="services")
     * @ORM\JoinTable(name="ozg_service_specialized_procedures",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="specialized_procedure_id", referencedColumnName="id")
     *   }
     * )
     */
    private $specializedProcedures;

    /**
     * @var Jurisdiction[]|Collection
     * @ORM\ManyToMany(targetEntity="Jurisdiction")
     * @ORM\JoinTable(name="ozg_service_rule_authority",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="jurisdiction_id", referencedColumnName="id")
     *   }
     * )
     */
    private $ruleAuthorities;

    /**
     * @var MinistryState[]|Collection
     * @ORM\ManyToMany(targetEntity="MinistryState", inversedBy="serviceAuthorities")
     * @ORM\JoinTable(name="ozg_service_authority_ministry_state",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ministry_state_id", referencedColumnName="id")
     *   }
     * )
     */
    private $authorityStateMinistries;

    /**
     * Toggle inheritance of state ministries from service system
     * @var bool
     *
     * @ORM\Column(name="authority_inherit_state_ministries", type="boolean")
     */
    protected $authorityInheritStateMinistries = true;

    /**
     * @var Bureau[]|Collection
     * @ORM\ManyToMany(targetEntity="Bureau")
     * @ORM\JoinTable(name="ozg_service_authority_bureau",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="bureau_id", referencedColumnName="id")
     *   }
     * )
     */
    private $authorityBureaus;

    /**
     * Toggle inheritance of bureaus from service system
     * @var bool
     *
     * @ORM\Column(name="authority_inherit_bureaus", type="boolean")
     */
    protected $authorityInheritBureaus = true;

    public function __construct()
    {
        $this->authorityBureaus = new ArrayCollection();
        $this->authorityStateMinistries = new ArrayCollection();
        $this->bureaus = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
        $this->jurisdictions = new ArrayCollection();
        $this->ruleAuthorities = new ArrayCollection();
        $this->serviceSolutions = new ArrayCollection();
        $this->specializedProcedures = new ArrayCollection();
        $this->stateMinistries = new ArrayCollection();
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
     * @param ServiceSolution $serviceSolution
     * @return self
     */
    public function addServiceSolution($serviceSolution): self
    {
        if (!$this->serviceSolutions->contains($serviceSolution)) {
            $this->serviceSolutions->add($serviceSolution);
            $serviceSolution->setService($this);
        }

        return $this;
    }

    /**
     * @param ServiceSolution $serviceSolution
     * @return self
     */
    public function removeServiceSolution($serviceSolution): self
    {
        if ($this->serviceSolutions->contains($serviceSolution)) {
            $this->serviceSolutions->removeElement($serviceSolution);
        }

        return $this;
    }

    /**
     * @return ServiceSolution[]|Collection
     */
    public function getServiceSolutions()
    {
        return $this->serviceSolutions;
    }

    /**
     * @param ServiceSolution[]|Collection $serviceSolutions
     */
    public function setServiceSolutions($serviceSolutions): void
    {
        $this->serviceSolutions = $serviceSolutions;
    }

    /**
     * Returns the published service solutions
     *
     * @return ServiceSolution[]|Collection
     */
    public function getPublishedServiceSolutions()
    {
        $publishedServiceSolutions = new ArrayCollection();
        $serviceSolutions = $this->getServiceSolutions();
        foreach ($serviceSolutions as $serviceSolution) {
            $solution = $serviceSolution->getSolution();
            if (null !== $solution && $solution->isPublished()) {
                $publishedServiceSolutions->add($serviceSolution);
            }
        }
        return $publishedServiceSolutions;
    }

    /**
     * @return string|null
     */
    public function getServiceType(): ?string
    {
        return $this->serviceType;
    }

    /**
     * @param string|null $serviceType
     */
    public function setServiceType(?string $serviceType): void
    {
        $this->serviceType = $serviceType;
    }

    /**
     * @return string|null
     */
    public function getLegalBasis(): ?string
    {
        return $this->legalBasis;
    }

    /**
     * @param string|null $legalBasis
     */
    public function setLegalBasis(?string $legalBasis): void
    {
        $this->legalBasis = $legalBasis;
    }

    /**
     * @return string|null
     */
    public function getLaws(): ?string
    {
        return $this->laws;
    }

    /**
     * @param string|null $laws
     */
    public function setLaws(?string $laws): void
    {
        $this->laws = $laws;
    }

    /**
     * @return string|null
     */
    public function getLawShortcuts(): ?string
    {
        return $this->lawShortcuts;
    }

    /**
     * @param string|null $lawShortcuts
     */
    public function setLawShortcuts(?string $lawShortcuts): void
    {
        $this->lawShortcuts = $lawShortcuts;
    }

    /**
     * @return bool
     */
    public function isRelevance1(): bool
    {
        return $this->relevance1;
    }

    /**
     * @param bool|null $relevance1
     */
    public function setRelevance1(?bool $relevance1): void
    {
        $this->relevance1 = (bool)$relevance1;
    }

    /**
     * @return bool
     */
    public function isRelevance2(): bool
    {
        return $this->relevance2;
    }

    /**
     * @param bool|null $relevance2
     */
    public function setRelevance2(?bool $relevance2): void
    {
        $this->relevance2 = (bool)$relevance2;
    }


    /**
     * @return ServiceSystem|null
     */
    public function getServiceSystem(): ?ServiceSystem
    {
        return $this->serviceSystem;
    }

    /**
     * @param ServiceSystem|null $serviceSystem
     */
    public function setServiceSystem(?ServiceSystem $serviceSystem)
    {
        $this->serviceSystem = $serviceSystem;
    }

    /**
     * @param Laboratory $laboratory
     * @return self
     */
    public function addLaboratory($laboratory): self
    {
        if (!$this->laboratories->contains($laboratory)) {
            $this->laboratories->add($laboratory);
            $laboratory->addService($this);
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
            $laboratory->removeService($this);
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
     * @param Jurisdiction $jurisdiction
     * @return self
     */
    public function addJurisdiction($jurisdiction): self
    {
        if (!$this->jurisdictions->contains($jurisdiction)) {
            $this->jurisdictions->add($jurisdiction);
            $jurisdiction->addService($this);
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
            $jurisdiction->removeService($this);
        }

        return $this;
    }

    /**
     * @return Jurisdiction[]|Collection
     */
    public function getJurisdictions()
    {
        if ($this->isInheritJurisdictions() && null !== $serviceSystem = $this->getServiceSystem()) {
            $ssJurisdictions = $serviceSystem->getJurisdictions();
            foreach ($ssJurisdictions as $jurisdiction) {
                $this->addJurisdiction($jurisdiction);
            }
        }
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
     * @return bool
     */
    public function isInheritJurisdictions(): bool
    {
        return $this->inheritJurisdictions;
    }

    /**
     * @param bool $inheritJurisdictions
     */
    public function setInheritJurisdictions(bool $inheritJurisdictions): void
    {
        $this->inheritJurisdictions = $inheritJurisdictions;
    }

    /**
     * @param Bureau $bureau
     * @return self
     */
    public function addBureau($bureau): self
    {
        if (!$this->bureaus->contains($bureau)) {
            $this->bureaus->add($bureau);
            $bureau->addService($this);
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
            $bureau->removeService($this);
        }

        return $this;
    }

    /**
     * @return Bureau[]|Collection
     */
    public function getBureaus()
    {
        if ($this->isInheritBureaus() && null !== $serviceSystem = $this->getServiceSystem()) {
            $ssBureaus = $serviceSystem->getBureaus();
            foreach ($ssBureaus as $bureau) {
                $this->addBureau($bureau);
            }
        }
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
     * @return bool
     */
    public function isInheritBureaus(): bool
    {
        return $this->inheritBureaus;
    }

    /**
     * @param bool $inheritBureaus
     */
    public function setInheritBureaus(bool $inheritBureaus): void
    {
        $this->inheritBureaus = $inheritBureaus;
    }

    /**
     * @param MinistryState $stateMinistry
     * @return self
     */
    public function addStateMinistry($stateMinistry): self
    {
        if (!$this->stateMinistries->contains($stateMinistry)) {
            $this->stateMinistries->add($stateMinistry);
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
        }

        return $this;
    }

    /**
     * @return MinistryState[]|Collection
     */
    public function getStateMinistries()
    {
        if ($this->isInheritStateMinistries() && null !== $serviceSystem = $this->getServiceSystem()) {
            $parentEntities = $serviceSystem->getStateMinistries();
            foreach ($parentEntities as $entity) {
                $this->addStateMinistry($entity);
            }
        }
        return $this->stateMinistries;
    }

    /**
     * @return bool
     */
    public function isInheritStateMinistries(): bool
    {
        return $this->inheritStateMinistries;
    }

    /**
     * @param bool $inheritStateMinistries
     */
    public function setInheritStateMinistries(bool $inheritStateMinistries): void
    {
        $this->inheritStateMinistries = $inheritStateMinistries;
    }

    /**
     * @param MinistryState[]|Collection $stateMinistries
     */
    public function setStateMinistries($stateMinistries): void
    {
        $this->stateMinistries = $stateMinistries;
    }

    /**
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function addSpecializedProcedure($specializedProcedure): self
    {
        if (!$this->specializedProcedures->contains($specializedProcedure)) {
            $this->specializedProcedures->add($specializedProcedure);
            $specializedProcedure->addService($this);
        }

        return $this;
    }

    /**
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function removeSpecializedProcedure($specializedProcedure): self
    {
        if ($this->specializedProcedures->contains($specializedProcedure)) {
            $this->specializedProcedures->removeElement($specializedProcedure);
            $specializedProcedure->removeService($this);
        }

        return $this;
    }

    /**
     * @return SpecializedProcedure[]|Collection
     */
    public function getSpecializedProcedures()
    {
        return $this->specializedProcedures;
    }

    /**
     * @param SpecializedProcedure[]|Collection $specializedProcedures
     */
    public function setSpecializedProcedures($specializedProcedures): void
    {
        $this->specializedProcedures = $specializedProcedures;
    }

    /**
     * @param Jurisdiction $ruleAuthority
     * @return self
     */
    public function addRuleAuthority($ruleAuthority): self
    {
        if (!$this->ruleAuthorities->contains($ruleAuthority)) {
            $this->ruleAuthorities->add($ruleAuthority);
        }

        return $this;
    }

    /**
     * @param Jurisdiction $ruleAuthority
     * @return self
     */
    public function removeRuleAuthority($ruleAuthority): self
    {
        if ($this->ruleAuthorities->contains($ruleAuthority)) {
            $this->ruleAuthorities->removeElement($ruleAuthority);
        }

        return $this;
    }

    /**
     * @return Jurisdiction[]|Collection
     */
    public function getRuleAuthorities()
    {
        if ($this->isInheritRuleAuthorities() && null !== $serviceSystem = $this->getServiceSystem()) {
            $ssRuleAuthorities = $serviceSystem->getRuleAuthorities();
            foreach ($ssRuleAuthorities as $ruleAuthority) {
                $this->addRuleAuthority($ruleAuthority);
            }
        }
        return $this->ruleAuthorities;
    }

    /**
     * @param Jurisdiction[]|Collection $ruleAuthorities
     */
    public function setRuleAuthorities($ruleAuthorities): void
    {
        $this->ruleAuthorities = $ruleAuthorities;
    }

    /**
     * @return bool
     */
    public function isInheritRuleAuthorities(): bool
    {
        return $this->inheritRuleAuthorities;
    }

    /**
     * @param bool $inheritRuleAuthorities
     */
    public function setInheritRuleAuthorities(bool $inheritRuleAuthorities): void
    {
        $this->inheritRuleAuthorities = $inheritRuleAuthorities;
    }

    /**
     * @param MinistryState $authorityStateMinistry
     * @return self
     */
    public function addAuthorityStateMinistry($authorityStateMinistry): self
    {
        if (!$this->authorityStateMinistries->contains($authorityStateMinistry)) {
            $this->authorityStateMinistries->add($authorityStateMinistry);
            $authorityStateMinistry->addServiceAuthority($this);
        }

        return $this;
    }

    /**
     * @param MinistryState $authorityStateMinistry
     * @return self
     */
    public function removeAuthorityStateMinistry($authorityStateMinistry): self
    {
        if ($this->authorityStateMinistries->contains($authorityStateMinistry)) {
            $this->authorityStateMinistries->removeElement($authorityStateMinistry);
            $authorityStateMinistry->removeServiceAuthority($this);
        }

        return $this;
    }

    /**
     * @return MinistryState[]|Collection
     */
    public function getAuthorityStateMinistries()
    {
        if ($this->isAuthorityInheritStateMinistries() && null !== $serviceSystem = $this->getServiceSystem()) {
            $parentEntities = $serviceSystem->getAuthorityStateMinistries();
            foreach ($parentEntities as $entity) {
                $this->addAuthorityStateMinistry($entity);
            }
        }
        return $this->authorityStateMinistries;
    }

    /**
     * @param MinistryState[]|Collection $authorityStateMinistries
     */
    public function setAuthorityStateMinistries($authorityStateMinistries): void
    {
        $this->authorityStateMinistries = $authorityStateMinistries;
    }

    /**
     * @return bool
     */
    public function isAuthorityInheritStateMinistries(): bool
    {
        return $this->authorityInheritStateMinistries;
    }

    /**
     * @param bool $authorityInheritStateMinistries
     */
    public function setAuthorityInheritStateMinistries(bool $authorityInheritStateMinistries): void
    {
        $this->authorityInheritStateMinistries = $authorityInheritStateMinistries;
    }

    /**
     * @param Bureau $authorityBureau
     * @return self
     */
    public function addAuthorityBureau($authorityBureau): self
    {
        if (!$this->authorityBureaus->contains($authorityBureau)) {
            $this->authorityBureaus->add($authorityBureau);
        }

        return $this;
    }

    /**
     * @param Bureau $authorityBureau
     * @return self
     */
    public function removeAuthorityBureau($authorityBureau): self
    {
        if ($this->authorityBureaus->contains($authorityBureau)) {
            $this->authorityBureaus->removeElement($authorityBureau);
        }

        return $this;
    }

    /**
     * @return Bureau[]|Collection
     */
    public function getAuthorityBureaus()
    {
        if ($this->isAuthorityInheritBureaus() && null !== $serviceSystem = $this->getServiceSystem()) {
            $parentEntities = $serviceSystem->getAuthorityBureaus();
            foreach ($parentEntities as $entity) {
                $this->addAuthorityBureau($entity);
            }
        }
        return $this->authorityBureaus;
    }

    /**
     * @param Bureau[]|Collection $authorityBureaus
     */
    public function setAuthorityBureaus($authorityBureaus): void
    {
        $this->authorityBureaus = $authorityBureaus;
    }

    /**
     * @return bool
     */
    public function isAuthorityInheritBureaus(): bool
    {
        return $this->authorityInheritBureaus;
    }

    /**
     * @param bool $authorityInheritBureaus
     */
    public function setAuthorityInheritBureaus(bool $authorityInheritBureaus): void
    {
        $this->authorityInheritBureaus = $authorityInheritBureaus;
    }

}
