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

use App\Entity\Api\ServiceBaseResult;
use App\Entity\Base\SluggableEntityTrait;
use App\Entity\Base\SluggableInterface;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\ModelRegion\ModelRegionProject;
use App\Entity\StateGroup\Bureau;
use App\Entity\StateGroup\CommuneType;
use App\Entity\StateGroup\MinistryState;
use App\Model\ServiceImplementationStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Leistungen (LeiKA)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service")
 * @ORM\HasLifecycleCallbacks
 */
class Service extends AbstractService implements SluggableInterface, HasMetaDateEntityInterface
{
    use SluggableEntityTrait;

    /**
     * @var string|null
     * @Gedmo\Slug(fields={"name", "serviceKey", "id"}, updatable=false)
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;

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
     * @ORM\Column(name="relevance1", type="boolean", nullable=true)
     */
    protected $relevance1 = false;

    /**
     * SDG2-Relevanz
     * @var bool
     *
     * @ORM\Column(name="relevance2", type="boolean", nullable=true)
     */
    protected $relevance2 = false;

    /**
     * Status
     * @var Status|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * @var ServiceSystem|null
     * @ORM\ManyToOne(targetEntity="App\Entity\ServiceSystem", inversedBy="services", cascade={"persist"})
     * @ORM\JoinColumn(name="service_system_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $serviceSystem;

    /**
     * @var Priority|null
     * @ORM\ManyToOne(targetEntity="Priority")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $priority;

    /**
     * @var ServiceSolution[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\ServiceSolution", mappedBy="service", cascade={"all"}, orphanRemoval=true)
     */
    private $serviceSolutions;

    /**
     * @var FederalInformationManagementType[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\FederalInformationManagementType", mappedBy="service", cascade={"all"}, orphanRemoval=true)
     */
    private $fimTypes;

    /**
     * @var Laboratory[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Laboratory", mappedBy="services")
     */
    private $laboratories;

    /**
     * @var Jurisdiction[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Jurisdiction", inversedBy="services")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\MinistryState")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Bureau", mappedBy="services")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\SpecializedProcedure", inversedBy="services")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Jurisdiction")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\MinistryState", inversedBy="serviceAuthorities")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Bureau")
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

    /**
     * @var ImplementationProjectService[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\ImplementationProjectService", mappedBy="service", cascade={"all"}, orphanRemoval=true)
     */
    private $implementationProjects;

    /**
     * @var Portal[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Portal")
     * @ORM\JoinTable(name="ozg_services_portals",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="portal_id", referencedColumnName="id")
     *   }
     * )
     */
    private $portals;

    /**
     * Notes
     *
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes = '';

    /**
     * @var CommuneType[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\CommuneType", inversedBy="services")
     * @ORM\JoinTable(name="ozg_service_commune_type",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="commune_type_id", referencedColumnName="id")
     *   }
     * )
     */
    private $communeTypes;

    /**
     * Toggle inheritance of commune types from service system
     * @var bool|null
     *
     * @ORM\Column(name="inherit_commune_types", type="boolean", nullable=true)
     */
    protected $inheritCommuneTypes = true;

    /**
     * The implementation status info from the implementation project service references
     *
     * @var ServiceImplementationStatus
     */
    private $serviceImplementationStatus;

    public function __construct()
    {
        $this->authorityBureaus = new ArrayCollection();
        $this->authorityStateMinistries = new ArrayCollection();
        $this->bureaus = new ArrayCollection();
        $this->communeTypes = new ArrayCollection();
        $this->fimTypes = new ArrayCollection();
        $this->implementationProjects = new ArrayCollection();
        $this->jurisdictions = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
        $this->portals = new ArrayCollection();
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
    public function addServiceSolution(ServiceSolution $serviceSolution): self
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
    public function removeServiceSolution(ServiceSolution $serviceSolution): self
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
     * @param FederalInformationManagementType $fimType
     * @return self
     */
    public function addFimType(FederalInformationManagementType $fimType): self
    {
        if (!$this->fimTypes->contains($fimType)) {
            $this->fimTypes->add($fimType);
            $fimType->setService($this);
        }

        return $this;
    }

    /**
     * @param FederalInformationManagementType $fimType
     * @return self
     */
    public function removeFimType(FederalInformationManagementType $fimType): self
    {
        if ($this->fimTypes->contains($fimType)) {
            $this->fimTypes->removeElement($fimType);
        }

        return $this;
    }

    /**
     * @param string $dataType
     * @return FederalInformationManagementType|null
     */
    public function getFimType(string $dataType): ?FederalInformationManagementType
    {
        $fimTypes = $this->getFimTypes();
        foreach ($fimTypes as $fimEntry) {
            /** @var FederalInformationManagementType $fimEntry */
            if ($fimEntry->getDataType() === $dataType) {
                return $fimEntry;
            }
        }
        return null;
    }

    /**
     * @return FederalInformationManagementType[]|Collection
     */
    public function getFimTypes()
    {
        $dataTypes = array_keys(FederalInformationManagementType::$mapTypes);
        $mapTypes = [];
        foreach ($dataTypes as $dataType) {
            $mapTypes[$dataType] = null;
        }
        foreach ($this->fimTypes as $fimEntry) {
            /** @var FederalInformationManagementType $fimEntry */
            if (($entryDataType = $fimEntry->getDataType()) && array_key_exists($entryDataType, $mapTypes)) {
                $mapTypes[$entryDataType] = $fimEntry;
            }
        }
        foreach ($dataTypes as $dataType) {
            if (null === $mapTypes[$dataType]) {
                $newEntry = new FederalInformationManagementType();
                $newEntry->setStatus(FederalInformationManagementType::STATUS_IN_PROGRESS);
                $newEntry->setDataType($dataType);
                $this->addFimType($newEntry);
            }
        }
        return $this->fimTypes;
    }

    /**
     * @param FederalInformationManagementType[]|Collection $fimTypes
     */
    public function setFimTypes($fimTypes): void
    {
        $this->fimTypes = $fimTypes;
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
     * @return ModelRegionProject[]|Collection
     */
    public function getPublishedModelRegionProjects()
    {
        return $this->getModelRegionProjects(true);
    }

    /**
     * @param bool $publishedOnly Only return projects for published solutions
     *
     * @return ModelRegionProject[]|Collection
     */
    public function getModelRegionProjects($publishedOnly = false)
    {
        $collection = new ArrayCollection();
        $serviceSolutions = $this->getServiceSolutions();
        foreach ($serviceSolutions as $serviceSolution) {
            if ((null !== $solution = $serviceSolution->getSolution())
                && (!$publishedOnly || $solution->isPublished())) {
                $mapCollection = $solution->getModelRegionProjects();
                foreach ($mapCollection as $modelRegionProject) {
                    if (!$collection->contains($modelRegionProject)) {
                        $collection->add($modelRegionProject);
                    }
                }
            }
        }
        return $collection;
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
    public function setServiceSystem(?ServiceSystem $serviceSystem): void
    {
        $this->serviceSystem = $serviceSystem;
    }

    /**
     * @return Priority|null
     */
    public function getPriority(): ?Priority
    {
        if (null === $this->priority && null !== $this->serviceSystem) {
            $this->priority = $this->serviceSystem->getPriority();
        }
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
     * @param Laboratory $laboratory
     * @return self
     */
    public function addLaboratory(Laboratory $laboratory): self
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
    public function removeLaboratory(Laboratory $laboratory): self
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
    public function addJurisdiction(Jurisdiction $jurisdiction): self
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
    public function removeJurisdiction(Jurisdiction $jurisdiction): self
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
    public function addBureau(Bureau $bureau): self
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
    public function removeBureau(Bureau $bureau): self
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
    public function addStateMinistry(MinistryState $stateMinistry): self
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
    public function removeStateMinistry(MinistryState $stateMinistry): self
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
    public function getRuleAuthorities(): Collection
    {
        if (null === $this->ruleAuthorities) {
            $this->ruleAuthorities = new ArrayCollection();
        }
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
     * @param CommuneType $communeType
     * @return self
     */
    public function addCommuneType($communeType): self
    {
        if (!$this->communeTypes->contains($communeType)) {
            $this->communeTypes->add($communeType);
        }

        return $this;
    }

    /**
     * @param CommuneType $communeType
     * @return self
     */
    public function removeCommuneType($communeType): self
    {
        if ($this->communeTypes->contains($communeType)) {
            $this->communeTypes->removeElement($communeType);
        }

        return $this;
    }

    /**
     * @return CommuneType[]|Collection
     */
    public function getCommuneTypes()
    {
        if ($this->isInheritCommuneTypes() && null !== $serviceSystem = $this->getServiceSystem()) {
            $parentEntities = $serviceSystem->getCommuneTypes();
            foreach ($parentEntities as $entity) {
                $this->addCommuneType($entity);
            }
        }
        return $this->communeTypes;
    }

    /**
     * @param CommuneType[]|Collection $communeTypes
     */
    public function setCommuneTypes($communeTypes): void
    {
        $this->communeTypes = $communeTypes;
    }

    /**
     * @return bool
     */
    public function isInheritCommuneTypes(): bool
    {
        return (bool)$this->inheritCommuneTypes;
    }

    /**
     * @param bool $inheritCommuneTypes
     */
    public function setInheritCommuneTypes(bool $inheritCommuneTypes): void
    {
        $this->inheritCommuneTypes = $inheritCommuneTypes;
    }

    /**
     * @param Bureau $authorityBureau
     * @return self
     */
    public function addAuthorityBureau(Bureau $authorityBureau): self
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
    public function removeAuthorityBureau(Bureau $authorityBureau): self
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

    /**
     * @param ImplementationProjectService $implementationProject
     * @return self
     */
    public function addImplementationProject(ImplementationProjectService $implementationProject): self
    {
        if (!$this->implementationProjects->contains($implementationProject)) {
            $this->implementationProjects->add($implementationProject);
            if (null !== $project = $implementationProject->getImplementationProject()) {
                $project->addService($implementationProject);
            }
        }

        return $this;
    }

    /**
     * @param ImplementationProjectService $implementationProject
     * @return self
     */
    public function removeImplementationProject(ImplementationProjectService $implementationProject): self
    {
        if ($this->implementationProjects->contains($implementationProject)) {
            $this->implementationProjects->removeElement($implementationProject);
            if (null !== $project = $implementationProject->getImplementationProject()) {
                $project->removeService($implementationProject);
            }
        }

        return $this;
    }

    /**
     * Return the implementation projects metadata for this service; if you need a list of the projects,
     * use getUniqueImplementationProjects
     *
     * @return ImplementationProjectService[]|Collection
     */
    public function getImplementationProjects()
    {
        return $this->implementationProjects;
    }

    /**
     * Set the implementation projects metadata for this service
     *
     * @param ImplementationProjectService[]|Collection $implementationProjects
     */
    public function setImplementationProjects($implementationProjects): void
    {
        $this->implementationProjects = $implementationProjects;
    }

    /**
     * Return all implementation projects for this service; this returns the actual projects, not the metadata
     * @return ImplementationProject[]
     */
    public function getUniqueImplementationProjects(): array
    {
        $list = [];
        foreach ($this->getImplementationProjects() as $sip) {
            if (null !== $project = $sip->getImplementationProject()) {
                $list[$project->getId()] = $project;
            }
        }
        return $list;
    }

    /**
     * Returns the implementation status info from the implementation project service references
     *
     * @return ServiceImplementationStatus
     */
    public function getImplementationProjectStatusInfo(): ServiceImplementationStatus
    {
        if (null === $this->serviceImplementationStatus) {
            $this->serviceImplementationStatus = new ServiceImplementationStatus($this);
        }
        return $this->serviceImplementationStatus;
    }

    /**
     * @param Portal $portal
     * @return self
     */
    public function addPortal(Portal $portal): self
    {
        if (!$this->portals->contains($portal)) {
            $this->portals->add($portal);
        }

        return $this;
    }

    /**
     * @param Portal $portal
     * @return self
     */
    public function removePortal(Portal $portal): self
    {
        if ($this->portals->contains($portal)) {
            $this->portals->removeElement($portal);
        }

        return $this;
    }

    /**
     * @return Portal[]|Collection
     */
    public function getPortals()
    {
        return $this->portals;
    }

    /**
     * @param Portal[]|Collection $portals
     */
    public function setPortals($portals): void
    {
        $this->portals = $portals;
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
     * Returns the service base result for the current service
     *
     * @return ServiceBaseResult|null
     */
    public function getServiceBaseResult(): ?ServiceBaseResult
    {
        if (null !== $fimDescription = $this->getFimType(FederalInformationManagementType::TYPE_DESCRIPTION)) {
            return $fimDescription->getServiceBaseResult();
        }
        return null;
    }
}
