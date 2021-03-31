<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\StateGroup;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Service;
use App\Entity\ServiceSystem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class CommuneType
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_commune_type")
 * @ORM\HasLifecycleCallbacks
 */
class CommuneType extends BaseNamedEntity
{
    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Toggle type is used for constituencies
     *
     * @var bool
     *
     * @ORM\Column(name="constituency", type="boolean", nullable=true)
     */
    protected $constituency = false;

    /**
     * One CommuneType has Many Communes.
     * @var Commune[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\StateGroup\Commune", mappedBy="communeType")
     */
    private $communes;

    /**
     * @var Service[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", mappedBy="communeTypes")
     */
    private $services;

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\ServiceSystem", mappedBy="communeTypes")
     */
    private $serviceSystems;

    public function __construct()
    {
        $this->communes = new ArrayCollection();
        $this->serviceSystems = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function addCommune($commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
            $commune->setCommuneType($this);
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
            $commune->setCommuneType(null);
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
     * @return bool
     */
    public function isConstituency(): bool
    {
        return $this->constituency;
    }

    /**
     * @param bool $constituency
     */
    public function setConstituency(bool $constituency): void
    {
        $this->constituency = $constituency;
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function addServiceSystem($serviceSystem): self
    {
        if (!$this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->add($serviceSystem);
            $serviceSystem->addCommuneType($this);
        }

        return $this;
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function removeServiceSystem($serviceSystem): self
    {
        if ($this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->removeElement($serviceSystem);
            $serviceSystem->removeCommuneType($this);
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

    /**
     * @param Service $service
     * @return self
     */
    public function addService($service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addCommuneType($this);
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
            $service->removeCommuneType($this);
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
