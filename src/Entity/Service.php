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

    public function __construct()
    {
        $this->laboratories = new ArrayCollection();
        $this->serviceSolutions = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
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
    public function addServiceSolution($serviceSolution)
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
    public function removeServiceSolution($serviceSolution)
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
        $this->relevance1 = (bool) $relevance1;
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
        $this->relevance2 = (bool) $relevance2;
    }


    /**
     * @return ServiceSystem|null
     */
    public function getServiceSystem()
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
    public function addLaboratory($laboratory)
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
    public function removeLaboratory($laboratory)
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
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $name = $this->getName();
        if (null === $name) {
            return '';
        }
        return $name;
    }

}
