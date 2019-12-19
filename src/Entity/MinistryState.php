<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Base\SoftdeletableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Ministerium Land
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_ministry_state")
 * @ORM\HasLifecycleCallbacks
 */
class MinistryState extends BaseNamedEntity
{
    use AddressTrait;
    use UrlTrait;

    /**
     * Short name
     * @var string|null
     *
     * @ORM\Column(type="string", name="short_name", length=255, nullable=true)
     */
    private $shortName;

    /**
     * @var Contact[]|Collection
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="ministryState", cascade={"all"})
     */
    private $contacts;

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\ManyToMany(targetEntity="ServiceSystem", inversedBy="stateMinistries")
     * @ORM\JoinTable(name="ozg_ministry_state_service_system",
     *     joinColumns={
     *     @ORM\JoinColumn(name="ministry_state_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   }
     * )
     */
    private $serviceSystems;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->serviceSystems = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @param string|null $shortName
     */
    public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function addContact($contact)
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setMinistryState($this);
        }

        return $this;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function removeContact($contact)
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            if ($contact instanceof SoftdeletableEntityInterface) {
                $contact->setDeletedAt(new \DateTime());
            }
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
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function addServiceSystem($serviceSystem)
    {
        if (!$this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->add($serviceSystem);
            $serviceSystem->addStateMinistry($this);
        }

        return $this;
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function removeServiceSystem($serviceSystem)
    {
        if ($this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->removeElement($serviceSystem);
            $serviceSystem->removeStateMinistry($this);
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

}
