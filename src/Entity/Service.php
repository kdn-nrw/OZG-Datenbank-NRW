<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\SoftdeletableEntityInterface;
use Mindbase\EntityBundle\Entity\SoftdeletableEntityTrait;


/**
 * Class Leistungen (LeiKA)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 */
class Service extends BaseBlamableEntity implements SoftdeletableEntityInterface, NamedEntityInterface
{
    use HideableEntityTrait;
    use SoftdeletableEntityTrait;


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
     * @var ServiceSystem
     * @ORM\ManyToOne(targetEntity="ServiceSystem", inversedBy="services", cascade={"persist"})
     */
    private $serviceSystem;

    /**
     * @var ServiceSolution[]|Collection
     * @ORM\OneToMany(targetEntity="ServiceSolution", mappedBy="service")
     */
    private $solutions;

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
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
     * @return ServiceSystem
     */
    public function getServiceSystem()
    {
        return $this->serviceSystem;
    }

    /**
     * @param ServiceSystem $serviceSystem
     */
    public function setServiceSystem($serviceSystem): void
    {
        $this->serviceSystem = $serviceSystem;
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
            return 'NULL';
        }
        return $name;
    }

}
