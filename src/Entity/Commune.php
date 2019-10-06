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
 * Class Commune
 *
 * @ORM\Entity(repositoryClass="App\Entity\Repository\CommuneRepository")
 * @ORM\Table(name="ozg_commune")
 * @ORM\HasLifecycleCallbacks
 */
class Commune extends BaseBlamableEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use AddressTrait;
    use UrlTrait;

    /**
     * Contact persons
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $contact;

    /**
     * @var Office[]|Collection
     * @ORM\OneToMany(targetEntity="Office", mappedBy="commune", cascade={"all"})
     */
    private $offices;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="ServiceProvider", inversedBy="communes")
     * @ORM\JoinTable(name="ozg_communes_service_provider",
     *     joinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id")
     *   }
     * )
     */
    private $serviceProviders;

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="Solution", mappedBy="communes")
     */
    private $solutions;

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
        $this->offices = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
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
     * @param Office $office
     * @return self
     */
    public function addOffice($office)
    {
        if (!$this->offices->contains($office)) {
            $this->offices->add($office);
            $office->setCommune($this);
        }

        return $this;
    }

    /**
     * @param Office $office
     * @return self
     */
    public function removeOffice($office)
    {
        if ($this->offices->contains($office)) {
            $this->offices->removeElement($office);
            if ($office instanceof SoftdeletableEntityInterface) {
                $office->setDeletedAt(new \DateTime());
            }
        }

        return $this;
    }

    /**
     * @return Office[]|Collection
     */
    public function getOffices()
    {
        return $this->offices;
    }

    /**
     * @param Office[]|Collection $offices
     */
    public function setOffices($offices): void
    {
        $this->offices = $offices;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function addServiceProvider($serviceProvider)
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
            $serviceProvider->addCommune($this);
        }

        return $this;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function removeServiceProvider($serviceProvider)
    {
        if ($this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->removeElement($serviceProvider);
            $serviceProvider->removeCommune($this);
        }

        return $this;
    }

    /**
     * @return ServiceProvider[]|Collection
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * @param ServiceProvider[]|Collection $serviceProviders
     */
    public function setServiceProviders($serviceProviders): void
    {
        $this->serviceProviders = $serviceProviders;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution)
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addCommune($this);
        }

        return $this;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function removeSolution($solution)
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            $solution->removeCommune($this);
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

}
