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

use App\Entity\Base\BaseBlamableEntity;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\NamedEntityTrait;


/**
 * Class SpecializedProcedure (Fachverfahren)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_specialized_procedure")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
 */
class SpecializedProcedure extends BaseBlamableEntity implements NamedEntityInterface, HasManufacturerEntityInterface, HasSolutionsEntityInterface
{
    public const DEFAULT_PARENT_APPLICATION_CATEGORY_ID = 1;

    use NamedEntityTrait;
    use HideableEntityTrait;

    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Solution", mappedBy="specializedProcedures")
     */
    private $solutions;

    /**
     * @var Manufacturer[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Manufacturer", mappedBy="specializedProcedures")
     */
    private $manufacturers;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\ServiceProvider", inversedBy="specializedProcedures")
     * @ORM\JoinTable(name="ozg_specialized_procedure_service_provider",
     *     joinColumns={
     *     @ORM\JoinColumn(name="specialized_procedure_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $serviceProviders;

    /**
     * @var Commune[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Commune", inversedBy="specializedProcedures")
     * @ORM\JoinTable(name="ozg_specialized_procedure_commune",
     *     joinColumns={
     *     @ORM\JoinColumn(name="specialized_procedure_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $communes;

    /**
     * @var Service[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", mappedBy="specializedProcedures")
     */
    private $services;


    public function __construct()
    {
        $this->communes = new ArrayCollection();
        $this->manufacturers = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
        $this->solutions = new ArrayCollection();
        $this->services = new ArrayCollection();
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
     * @param Manufacturer $manufacturer
     * @return self
     */
    public function addManufacturer($manufacturer): self
    {
        if (!$this->manufacturers->contains($manufacturer)) {
            $this->manufacturers->add($manufacturer);
            $manufacturer->addSpecializedProcedure($this);
        }

        return $this;
    }

    /**
     * @param Manufacturer $manufacturer
     * @return self
     */
    public function removeManufacturer($manufacturer): self
    {
        if ($this->manufacturers->contains($manufacturer)) {
            $this->manufacturers->removeElement($manufacturer);
            $manufacturer->removeSpecializedProcedure($this);
        }

        return $this;
    }

    /**
     * @return Manufacturer[]|Collection
     */
    public function getManufacturers(): Collection
    {
        return $this->manufacturers;
    }

    /**
     * @param Manufacturer[]|Collection $manufacturers
     */
    public function setManufacturers($manufacturers): void
    {
        $this->manufacturers = $manufacturers;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addSpecializedProcedure($this);
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
            $solution->removeSpecializedProcedure($this);
        }

        return $this;
    }

    /**
     * @return Solution[]|Collection
     */
    public function getSolutions(): Collection
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
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function addServiceProvider($serviceProvider): self
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
            $serviceProvider->addSpecializedProcedure($this);
        }

        return $this;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function removeServiceProvider($serviceProvider): self
    {
        if ($this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->removeElement($serviceProvider);
            $serviceProvider->removeSpecializedProcedure($this);
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
     * @param Commune $commune
     * @return self
     */
    public function addCommune($commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
            $commune->addSpecializedProcedure($this);
        }

        return $this;
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function removeCommune($commune): self
    {
        if ($this->communes->contains($commune)) {
            $this->communes->removeElement($commune);
            $commune->removeSpecializedProcedure($this);
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
     * @param Service $service
     * @return self
     */
    public function addService($service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addSpecializedProcedure($this);
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
            $service->removeSpecializedProcedure($this);
        }

        return $this;
    }

    /**
     * @return Service[]|Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param Service[]|Collection $services
     */
    public function setServices($services): void
    {
        $this->services = $services;
    }
}
