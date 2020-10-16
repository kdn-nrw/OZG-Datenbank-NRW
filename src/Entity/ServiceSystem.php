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

use App\Entity\Base\SluggableEntityTrait;
use App\Entity\Base\SluggableInterface;
use App\Entity\Base\SoftdeletableEntityInterface;
use App\Entity\StateGroup\Bureau;
use App\Entity\StateGroup\CommuneType;
use App\Entity\StateGroup\MinistryState;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class OZG service systems
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service_system")
 * @ORM\HasLifecycleCallbacks
 */
class ServiceSystem extends AbstractService implements SluggableInterface
{
    use ContactTextTrait;
    use SluggableEntityTrait;
    /**
     * @var string|null
     * @Gedmo\Slug(fields={"name", "serviceKey", "id"}, updatable=false)
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * Status
     * @var Status|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Service", mappedBy="serviceSystem", cascade={"persist"})
     */
    private $services;

    /**
     * @var Jurisdiction[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Jurisdiction", inversedBy="serviceSystems")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\ImplementationProject", mappedBy="serviceSystems")
     */
    private $implementationProjects;

    /**
     * @var Laboratory[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Laboratory", mappedBy="serviceSystems")
     */
    private $laboratories;

    /**
     * @var MinistryState[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\MinistryState", mappedBy="serviceSystems")
     */
    private $stateMinistries;

    /**
     * @var Bureau[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Bureau", mappedBy="serviceSystems")
     */
    private $bureaus;

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Solution")
     * @ORM\JoinTable(name="ozg_service_system_solution",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   }
     * )
     */
    private $solutions;

    /**
     * @var Jurisdiction[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Jurisdiction")
     * @ORM\JoinTable(name="ozg_service_system_rule_authority",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="jurisdiction_id", referencedColumnName="id")
     *   }
     * )
     */
    private $ruleAuthorities;

    /**
     * @var MinistryState[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\MinistryState")
     * @ORM\JoinTable(name="ozg_service_system_authority_ministry_state",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ministry_state_id", referencedColumnName="id")
     *   }
     * )
     */
    private $authorityStateMinistries;

    /**
     * @var Bureau[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Bureau")
     * @ORM\JoinTable(name="ozg_service_system_authority_bureau",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="bureau_id", referencedColumnName="id")
     *   }
     * )
     */
    private $authorityBureaus;

    /**
     * @var CommuneType[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\CommuneType", inversedBy="serviceSystems")
     * @ORM\JoinTable(name="ozg_service_system_commune_type",
     *     joinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="commune_type_id", referencedColumnName="id")
     *   }
     * )
     */
    private $communeTypes;

    public function __construct()
    {
        $this->authorityBureaus = new ArrayCollection();
        $this->authorityStateMinistries = new ArrayCollection();
        $this->bureaus = new ArrayCollection();
        $this->communeTypes = new ArrayCollection();
        $this->implementationProjects = new ArrayCollection();
        $this->jurisdictions = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
        $this->ruleAuthorities = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->solutions = new ArrayCollection();
        $this->stateMinistries = new ArrayCollection();
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
                $service->setDeletedAt(new DateTime());
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
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            //$solution->addServiceSystem($this);
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
            //$solution->removeServiceSystem($this);
        }

        return $this;
    }

    /**
     * @return Solution[]|Collection
     */
    public function getSolutions()
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
     * @param MinistryState $authorityStateMinistry
     * @return self
     */
    public function addAuthorityStateMinistry($authorityStateMinistry): self
    {
        if (!$this->authorityStateMinistries->contains($authorityStateMinistry)) {
            $this->authorityStateMinistries->add($authorityStateMinistry);
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
        }

        return $this;
    }

    /**
     * @return MinistryState[]|Collection
     */
    public function getAuthorityStateMinistries()
    {
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
        return $this->authorityBureaus;
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
        $services = $this->getServices();
        foreach ($services as $service) {
            $mapCollection = $service->getModelRegionProjects($publishedOnly);
            foreach ($mapCollection as $modelRegionProject) {
                if (!$collection->contains($modelRegionProject)) {
                    $collection->add($modelRegionProject);
                }
            }
        }
        return $collection;
    }

    /**
     * Copy inherited values to services
     */
    public function saveInheritedValues(): void
    {
        // Save inherited properties in service entities
        $jurisdictions = $this->getJurisdictions();
        $bureaus = $this->getBureaus();
        $communeTypes = $this->getCommuneTypes();
        $stateMinistries = $this->getAuthorityStateMinistries();
        $authorityBureaus = $this->getAuthorityBureaus();
        $authorityBureausEnabled = $this->getAuthorityBureausEnabled();
        $ruleAuthorities = $this->getRuleAuthorities();
        $authorityStateMinistries = $this->getAuthorityStateMinistries();
        $authorityStateMinistriesEnabled = $this->getAuthorityStateMinistriesEnabled();
        $services = $this->getServices();
        foreach ($services as $service) {
            if ($service->isInheritJurisdictions()) {
                foreach ($jurisdictions as $jurisdiction) {
                    $service->addJurisdiction($jurisdiction);
                }
            }
            if ($service->isInheritBureaus()) {
                foreach ($bureaus as $bureau) {
                    $service->addBureau($bureau);
                }
            }
            if ($service->isInheritStateMinistries()) {
                foreach ($stateMinistries as $stateMinistry) {
                    $service->addStateMinistry($stateMinistry);
                }
            }
            if ($service->isInheritCommuneTypes()) {
                foreach ($communeTypes as $communeType) {
                    $service->addCommuneType($communeType);
                }
            }
            if ($service->isInheritRuleAuthorities()) {
                foreach ($ruleAuthorities as $ruleAuthority) {
                    $service->addRuleAuthority($ruleAuthority);
                }
            }
            if ($authorityBureausEnabled && $service->isAuthorityInheritBureaus()) {
                foreach ($authorityBureaus as $authorityBureau) {
                    $service->addAuthorityBureau($authorityBureau);
                }
            }
            if ($authorityStateMinistriesEnabled && $service->isAuthorityInheritStateMinistries()) {
                foreach ($authorityStateMinistries as $stateMinistry) {
                    $service->addAuthorityStateMinistry($stateMinistry);
                }
            }
        }
    }
}
