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

namespace App\Entity;

use App\Entity\Application\ApplicationInterface;
use App\Entity\Application\ApplicationModule;
use App\Entity\Base\BaseNamedEntity;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Application
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_application")
 * @ORM\HasLifecycleCallbacks
 */
class Application extends BaseNamedEntity implements HasManufacturerEntityInterface
{
    use AddressTrait;
    use CategoryTrait;
    use UrlTrait;

    public const IN_HOUSE_DEVELOPMENT_NO = 0;
    public const IN_HOUSE_DEVELOPMENT_YES = 1;
    public const IN_HOUSE_DEVELOPMENT_YES_REUSE = 2;

    public static $inHouseDevelopmentChoices = [
        self::IN_HOUSE_DEVELOPMENT_NO => 'app.application.entity.in_house_development_choices.no',
        self::IN_HOUSE_DEVELOPMENT_YES => 'app.application.entity.in_house_development_choices.yes',
        self::IN_HOUSE_DEVELOPMENT_YES_REUSE => 'app.application.entity.in_house_development_choices.yes_reuse',
    ];

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var Manufacturer[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Manufacturer")
     * @ORM\JoinTable(name="ozg_application_manufacturer",
     *     joinColumns={
     *     @ORM\JoinColumn(name="application_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $manufacturers;

    /**
     * @var ApplicationCategory[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\ApplicationCategory")
     * @ORM\JoinTable(name="ozg_application_category_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *   }
     * )
     */
    private $categories;

    /**
     * @var Commune[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Commune")
     * @ORM\JoinTable(name="ozg_application_commune",
     *     joinColumns={
     *     @ORM\JoinColumn(name="application_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $communes;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\ServiceProvider")
     * @ORM\JoinTable(name="ozg_application_service_provider",
     *     joinColumns={
     *     @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id")
     *   }
     * )
     */
    private $serviceProviders;

    /**
     * @var string|null
     *
     * @ORM\Column(name="accessibility", type="text", nullable=true)
     */
    private $accessibility = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="privacy", type="text", nullable=true)
     */
    private $privacy = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="archive", type="text", nullable=true)
     */
    private $archive = '';

    /**
     * In house development type
     *
     * @var int
     *
     * @ORM\Column(name="in_house_development", type="integer", nullable=true)
     */
    protected $inHouseDevelopment = self::IN_HOUSE_DEVELOPMENT_NO;

    /**
     * Application modules
     * @var ApplicationModule[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Application\ApplicationModule", mappedBy="application", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $applicationModules;

    /**
     * Application interfaces
     * @var ApplicationInterface[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Application\ApplicationInterface", mappedBy="application", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $applicationInterfaces;

    public function __construct()
    {
        $this->applicationModules = new ArrayCollection();
        $this->applicationInterfaces = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->communes = new ArrayCollection();
        $this->manufacturers = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
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
     * @param Commune $commune
     * @return self
     */
    public function addCommune($commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
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
     * @param Manufacturer $manufacturer
     * @return self
     */
    public function addManufacturer($manufacturer): self
    {
        if (!$this->manufacturers->contains($manufacturer)) {
            $this->manufacturers->add($manufacturer);
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
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function addServiceProvider($serviceProvider): self
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
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
     * @return string|null
     */
    public function getAccessibility(): ?string
    {
        return $this->accessibility;
    }

    /**
     * @param string|null $accessibility
     */
    public function setAccessibility(?string $accessibility): void
    {
        $this->accessibility = $accessibility;
    }

    /**
     * @return string|null
     */
    public function getPrivacy(): ?string
    {
        return $this->privacy;
    }

    /**
     * @param string|null $privacy
     */
    public function setPrivacy(?string $privacy): void
    {
        $this->privacy = $privacy;
    }

    /**
     * @return string|null
     */
    public function getArchive(): ?string
    {
        return $this->archive;
    }

    /**
     * @param string|null $archive
     */
    public function setArchive(?string $archive): void
    {
        $this->archive = $archive;
    }

    /**
     * @return int
     */
    public function getInHouseDevelopment(): int
    {
        return $this->inHouseDevelopment ?? self::IN_HOUSE_DEVELOPMENT_NO;
    }

    /**
     * @param int $inHouseDevelopment
     */
    public function setInHouseDevelopment(int $inHouseDevelopment): void
    {
        $this->inHouseDevelopment = $inHouseDevelopment;
    }


    /**
     * @param ApplicationModule $applicationModule
     * @return self
     */
    public function addApplicationModule(ApplicationModule $applicationModule): self
    {
        if (!$this->applicationModules->contains($applicationModule)) {
            $this->applicationModules->add($applicationModule);
            $applicationModule->setApplication($this);
        }

        return $this;
    }

    /**
     * @param ApplicationModule $applicationModule
     * @return self
     */
    public function removeApplicationModule(ApplicationModule $applicationModule): self
    {
        if ($this->applicationModules->contains($applicationModule)) {
            $this->applicationModules->removeElement($applicationModule);
        }

        return $this;
    }

    /**
     * @return ApplicationModule[]|Collection
     */
    public function getApplicationModules()
    {
        return $this->applicationModules;
    }

    /**
     * @param ApplicationModule[]|Collection $applicationModules
     */
    public function setApplicationModules($applicationModules): void
    {
        $this->applicationModules = $applicationModules;
    }

    /**
     * @param ApplicationInterface $applicationInterface
     * @return self
     */
    public function addApplicationInterface(ApplicationInterface $applicationInterface): self
    {
        if (!$this->applicationInterfaces->contains($applicationInterface)) {
            $this->applicationInterfaces->add($applicationInterface);
            $applicationInterface->setApplication($this);
        }

        return $this;
    }

    /**
     * @param ApplicationInterface $applicationInterface
     * @return self
     */
    public function removeApplicationInterface(ApplicationInterface $applicationInterface): self
    {
        if ($this->applicationInterfaces->contains($applicationInterface)) {
            $this->applicationInterfaces->removeElement($applicationInterface);
        }

        return $this;
    }

    /**
     * @return ApplicationInterface[]|Collection
     */
    public function getApplicationInterfaces()
    {
        return $this->applicationInterfaces;
    }

    /**
     * @param ApplicationInterface[]|Collection $applicationInterfaces
     */
    public function setApplicationInterfaces($applicationInterfaces): void
    {
        $this->applicationInterfaces = $applicationInterfaces;
    }

}
