<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\NamedEntityTrait;


/**
 * Class OZG Leistungen
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service_system")
 * @ORM\HasLifecycleCallbacks
 */
class ServiceSystem extends BaseEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;

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

    /**
     * ozgvollzug
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $execution;

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
     * ozgbeschreibung
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var Situation
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
     * @ORM\OneToMany(targetEntity="Service", mappedBy="serviceSystem")
     */
    private $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
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
     * @return Situation
     */
    public function getSituation()
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
     * @return string|null
     */
    public function getExecution(): ?string
    {
        return $this->execution;
    }

    /**
     * @param string|null $execution
     */
    public function setExecution(?string $execution): void
    {
        $this->execution = $execution;
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
     * @param Service $service
     * @return self
     */
    public function addService($service)
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
    public function removeService($service)
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

}
