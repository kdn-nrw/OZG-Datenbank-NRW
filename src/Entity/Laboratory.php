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
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\NamedEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Laboratory (Labor)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_laboratory")
 * @ORM\HasLifecycleCallbacks
 */
class Laboratory extends BaseBlamableEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use UrlTrait;

    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Other participants
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $participantsOther;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="ServiceProvider", inversedBy="laboratories")
     * @ORM\JoinTable(name="ozg_laboratory_service_provider",
     *     joinColumns={
     *     @ORM\JoinColumn(name="laboratory_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id")
     *   }
     * )
     */
    private $serviceProviders;

    /**
     * @var Service[]|Collection
     * @ORM\ManyToMany(targetEntity="Service", inversedBy="laboratories")
     * @ORM\JoinTable(name="ozg_laboratory_service",
     *     joinColumns={
     *     @ORM\JoinColumn(name="laboratory_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     *   }
     * )
     */
    private $services;

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\ManyToMany(targetEntity="ServiceSystem", inversedBy="laboratories")
     * @ORM\JoinTable(name="ozg_laboratory_service_system",
     *     joinColumns={
     *     @ORM\JoinColumn(name="laboratory_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   }
     * )
     */
    private $serviceSystems;

    /**
     * Implementation url
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $implementationUrl;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
        $this->serviceSystems = new ArrayCollection();
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
     * @return string|null
     */
    public function getParticipantsOther(): ?string
    {
        return $this->participantsOther;
    }

    /**
     * @param string|null $participantsOther
     */
    public function setParticipantsOther(?string $participantsOther): void
    {
        $this->participantsOther = $participantsOther;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function addServiceProvider($serviceProvider)
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
            $serviceProvider->addLaboratory($this);
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
            $serviceProvider->removeLaboratory($this);
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
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function addServiceSystem($serviceSystem)
    {
        if (!$this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->add($serviceSystem);
            $serviceSystem->addLaboratory($this);
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
            $serviceSystem->removeLaboratory($this);
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
     * @return string|null
     */
    public function getImplementationUrl(): ?string
    {
        return $this->implementationUrl;
    }

    /**
     * @param string|null $implementationUrl
     */
    public function setImplementationUrl(?string $implementationUrl): void
    {
        $this->implementationUrl = $implementationUrl;
    }

    /**
     * @param Service $service
     * @return self
     */
    public function addService($service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addLaboratory($this);
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
            $service->removeLaboratory($this);
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
