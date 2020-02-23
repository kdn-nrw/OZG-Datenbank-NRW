<?php

namespace App\Entity;

use App\Entity\Base\BaseBlamableEntity;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\NamedEntityInterface;
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
class Service extends BaseBlamableEntity implements NamedEntityInterface
{
    use HideableEntityTrait;


    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    protected $name;

    /**
     * Leika-ID
     * @var string|null
     *
     * @ORM\Column(type="string", name="service_key", length=255, nullable=true)
     */
    private $serviceKey;

    /**
     * ozgbeschreibung
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

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

    public function __construct()
    {
        $this->bureaus = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
        $this->jurisdictions = new ArrayCollection();
        $this->serviceSolutions = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
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
            foreach ($ssJurisdictions as $bureau) {
                $this->addJurisdiction($bureau);
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
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $name = (string)$this->getName();
        if (null === $name) {
            return '';
        }
        return $name;
    }

}
