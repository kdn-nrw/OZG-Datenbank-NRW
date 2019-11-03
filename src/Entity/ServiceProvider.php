<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
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

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
        $this->communes = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
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
    public function addSolution($solution)
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->setServiceProvider($this);
        }
    }

    /**
     * @param Solution $solution
     */
    public function removeSolution($solution)
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            if ($solution instanceof SoftdeletableEntityInterface) {
                $solution->setDeletedAt(new \DateTime());
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
    public function addCommune($commune)
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
    public function removeCommune($commune)
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
    public function addLaboratory($laboratory)
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
    public function removeLaboratory($laboratory)
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

}
