<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Base\SoftdeletableEntityInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Dienstleister
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service_provider")
 * @ORM\HasLifecycleCallbacks
 */
class ServiceProvider extends BaseNamedEntity
{
    use AddressTrait;
    use UrlTrait;

    /**
     * Contact
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $contact;

    /**
     * @var Collection|Solution[]
     *
     * @ORM\OneToMany(targetEntity="Solution", mappedBy="serviceProvider", cascade={"all"})
     */
    private $solutions;

    /**
     * @var Commune[]|Collection
     * @ORM\ManyToMany(targetEntity="Commune", mappedBy="serviceProviders")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $communes;

    /**
     * @var Laboratory[]|Collection
     * @ORM\ManyToMany(targetEntity="Laboratory", mappedBy="serviceProviders")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $laboratories;

    /**
     * @var Contact[]|Collection
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="serviceProvider", cascade={"all"})
     */
    private $contacts;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="SpecializedProcedure", mappedBy="serviceProviders")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $specializedProcedures;

    public function __construct()
    {
        $this->communes = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->solutions = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
        $this->specializedProcedures = new ArrayCollection();
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
     * @param Solution $solution
     */
    public function addSolution($solution): void
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->setServiceProvider($this);
        }
    }

    /**
     * @param Solution $solution
     */
    public function removeSolution($solution): void
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            if ($solution instanceof SoftdeletableEntityInterface) {
                $solution->setDeletedAt(new DateTime());
            }
        }
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
     * @param Commune $commune
     * @return ServiceProvider
     */
    public function addCommune($commune): ServiceProvider
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
            $commune->addServiceProvider($this);
        }

        return $this;
    }

    /**
     * @param Commune $commune
     * @return ServiceProvider
     */
    public function removeCommune($commune): ServiceProvider
    {
        if ($this->communes->contains($commune)) {
            $this->communes->removeElement($commune);
            $commune->removeServiceProvider($this);
        }

        return $this;
    }

    /**
     * @return Commune[]|Collection
     */
    public function getCommunes()
    {
        return $this->communes;
    }

    /**
     * @param Commune[]|Collection $communes
     */
    public function setCommunes($communes): void
    {
        $this->communes = $communes;
    }

    /**
     * @param Laboratory $laboratory
     * @return ServiceProvider
     */
    public function addLaboratory($laboratory): ServiceProvider
    {
        if (!$this->laboratories->contains($laboratory)) {
            $this->laboratories->add($laboratory);
            $laboratory->addServiceProvider($this);
        }

        return $this;
    }

    /**
     * @param Laboratory $laboratory
     * @return ServiceProvider
     */
    public function removeLaboratory($laboratory): ServiceProvider
    {
        if ($this->laboratories->contains($laboratory)) {
            $this->laboratories->removeElement($laboratory);
            $laboratory->removeServiceProvider($this);
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
     * @param Contact $contact
     * @return self
     */
    public function addContact($contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setServiceProvider($this);
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
            if ($contact instanceof SoftdeletableEntityInterface) {
                $contact->setDeletedAt(new DateTime());
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
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function addSpecializedProcedure($specializedProcedure): self
    {
        if (!$this->specializedProcedures->contains($specializedProcedure)) {
            $this->specializedProcedures->add($specializedProcedure);
            $specializedProcedure->addServiceProvider($this);
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
            $specializedProcedure->removeServiceProvider($this);
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
     * Returns the unique manufacturer list with the manufacturer determined through the specialized procedures
     * @return ArrayCollection
     */
    public function getManufacturers()
    {
        $manufacturers = new ArrayCollection();
        $specializedProcedures = $this->getSpecializedProcedures();
        foreach ($specializedProcedures as $specializedProcedure) {
            $spManufacturers = $specializedProcedure->getManufacturers();
            foreach ($spManufacturers as $manufacturer) {
                if (!$manufacturers->contains($manufacturer)) {
                    $manufacturers->add($manufacturer);
                }
            }
        }
        return $manufacturers;
    }

}
