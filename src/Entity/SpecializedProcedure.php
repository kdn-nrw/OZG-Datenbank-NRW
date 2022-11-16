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

use App\Entity\Application\ApplicationAccessibilityDocument;
use App\Entity\Application\ApplicationCategory;
use App\Entity\Application\ApplicationInterface;
use App\Entity\Application\ApplicationModule;
use App\Entity\Base\BaseBlamableEntity;
use App\Entity\Base\HasDocumentsEntityInterface;
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\NamedEntityTrait;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class SpecializedProcedure (Fachverfahren)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_specialized_procedure")
 * @ORM\HasLifecycleCallbacks
 */
class SpecializedProcedure extends BaseBlamableEntity implements
    NamedEntityInterface,
    HasDocumentsEntityInterface,
    HasManufacturerEntityInterface,
    HasSolutionsEntityInterface,
    HideableEntityInterface
{
    public const DEFAULT_PARENT_APPLICATION_CATEGORY_ID = 1;

    use NamedEntityTrait;
    use HideableEntityTrait;
    use AddressTrait;
    use CategoryTrait;
    use UrlTrait;

    public const IN_HOUSE_DEVELOPMENT_NO = 0;
    public const IN_HOUSE_DEVELOPMENT_YES = 1;
    public const IN_HOUSE_DEVELOPMENT_YES_REUSE = 2;

    public static $inHouseDevelopmentChoices = [
        self::IN_HOUSE_DEVELOPMENT_NO => 'app.specialized_procedure.entity.in_house_development_choices.no',
        self::IN_HOUSE_DEVELOPMENT_YES => 'app.specialized_procedure.entity.in_house_development_choices.yes',
        self::IN_HOUSE_DEVELOPMENT_YES_REUSE => 'app.specialized_procedure.entity.in_house_development_choices.yes_reuse',
    ];

    public const ACCESSIBILITY_TEST_RESULT_TYPE_1 = 1;
    public const ACCESSIBILITY_TEST_RESULT_TYPE_2 = 2;
    public const ACCESSIBILITY_TEST_RESULT_TYPE_3 = 3;
    public const ACCESSIBILITY_TEST_RESULT_TYPE_4 = 4;
    public const ACCESSIBILITY_TEST_RESULT_TYPE_5 = 5;

    public const ACCESSIBILITY_TEST_RESULT_TYPES = [
        0 => 'app.specialized_procedure.entity.accessibility_test_result_type_choices.empty',
        self::ACCESSIBILITY_TEST_RESULT_TYPE_1 => 'app.specialized_procedure.entity.accessibility_test_result_type_choices.1',
        self::ACCESSIBILITY_TEST_RESULT_TYPE_2 => 'app.specialized_procedure.entity.accessibility_test_result_type_choices.2',
        self::ACCESSIBILITY_TEST_RESULT_TYPE_3 => 'app.specialized_procedure.entity.accessibility_test_result_type_choices.3',
        self::ACCESSIBILITY_TEST_RESULT_TYPE_4 => 'app.specialized_procedure.entity.accessibility_test_result_type_choices.4',
        self::ACCESSIBILITY_TEST_RESULT_TYPE_5 => 'app.specialized_procedure.entity.accessibility_test_result_type_choices.5',
    ];

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
     * @var ApplicationCategory[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Application\ApplicationCategory")
     * @ORM\JoinTable(name="ozg_specialized_procedure_category_mm",
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
     * @var Service[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", mappedBy="specializedProcedures")
     */
    private $services;

    /**
     * @var Organisation[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Organisation")
     * @ORM\JoinTable(name="ozg_specialized_procedure_business_premises",
     *     joinColumns={
     *     @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     *   }
     * )
     */
    private $businessPremises;

    /**
     * @var string|null
     *
     * @ORM\Column(name="accessibility_test_conducted", type="text", nullable=true)
     */
    private $accessibilityTestConducted = '';

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

    /**
     * @var Organisation[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Organisation")
     * @ORM\JoinTable(name="ozg_specialized_procedure_accessibility_test_organisations",
     *     joinColumns={
     *     @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     *   }
     * )
     */
    private $accessibilityTestOrganisations;

    /**
     * @var string|null
     *
     * @ORM\Column(name="accessibility_test_organisation_others", type="text", nullable=true)
     */
    private $accessibilityTestOrganisationOthers = '';

    /**
     * Accessibility self testing
     *
     * @var bool
     *
     * @ORM\Column(name="accessibility_self_testing", type="boolean", nullable=true)
     */
    protected $accessibilitySelfTesting = false;

    /**
     * The accessibility test result type
     *
     * @ORM\Column(type="integer", name="accessibility_test_result_type")
     * @var int|null
     */
    protected $accessibilityTestResultType;

    /**
     * @var ApplicationAccessibilityDocument[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Application\ApplicationAccessibilityDocument", mappedBy="application", cascade={"persist", "remove"})
     */
    private $accessibilityDocuments;

    /**
     * Initialize the entity instance
     */
    public function __construct()
    {
        $this->accessibilityTestOrganisations = new ArrayCollection();
        $this->applicationModules = new ArrayCollection();
        $this->applicationInterfaces = new ArrayCollection();
        $this->businessPremises = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->communes = new ArrayCollection();
        $this->accessibilityDocuments = new ArrayCollection();
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
    public function addManufacturer(Manufacturer $manufacturer): self
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
    public function removeManufacturer(Manufacturer $manufacturer): self
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
    public function addSolution(Solution $solution): self
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
    public function removeSolution(Solution $solution): self
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
    public function addServiceProvider(ServiceProvider $serviceProvider): self
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
    public function removeServiceProvider(ServiceProvider $serviceProvider): self
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
    public function addCommune(Commune $commune): self
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
    public function removeCommune(Commune $commune): self
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
    public function addService(Service $service): self
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
    public function removeService(Service $service): self
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

    /**
     * Adds a business premise
     *
     * @param Organisation $businessPremise
     * @return self
     */
    public function addBusinessPremise(Organisation $businessPremise): self
    {
        if (!$this->businessPremises->contains($businessPremise)) {
            $this->businessPremises->add($businessPremise);
        }

        return $this;
    }

    /**
     * Removes a business premise
     * @param Organisation $businessPremise
     * @return self
     */
    public function removeBusinessPremise(Organisation $businessPremise): self
    {
        if ($this->businessPremises->contains($businessPremise)) {
            $this->businessPremises->removeElement($businessPremise);
        }

        return $this;
    }

    /**
     * Returns the business premises
     *
     * @return Organisation[]|Collection
     */
    public function getBusinessPremises()
    {
        return $this->businessPremises;
    }

    /**
     * Sets the business premises
     *
     * @param Organisation[]|Collection $businessPremises
     */
    public function setBusinessPremises($businessPremises): void
    {
        $this->businessPremises = $businessPremises;
    }

    /**
     * @return string|null
     */
    public function getAccessibilityTestConducted(): ?string
    {
        return $this->accessibilityTestConducted;
    }

    /**
     * @param string|null $accessibilityTestConducted
     */
    public function setAccessibilityTestConducted(?string $accessibilityTestConducted): void
    {
        $this->accessibilityTestConducted = $accessibilityTestConducted;
    }

    /**
     * @param Organisation $accessibilityTestOrganisation
     * @return self
     */
    public function addAccessibilityTestOrganisation(Organisation $accessibilityTestOrganisation): self
    {
        if (!$this->accessibilityTestOrganisations->contains($accessibilityTestOrganisation)) {
            $this->accessibilityTestOrganisations->add($accessibilityTestOrganisation);
        }

        return $this;
    }

    /**
     * @param Organisation $accessibilityTestOrganisation
     * @return self
     */
    public function removeAccessibilityTestOrganisation(Organisation $accessibilityTestOrganisation): self
    {
        if ($this->accessibilityTestOrganisations->contains($accessibilityTestOrganisation)) {
            $this->accessibilityTestOrganisations->removeElement($accessibilityTestOrganisation);
        }

        return $this;
    }

    /**
     * @return Organisation[]|Collection
     */
    public function getAccessibilityTestOrganisations(): Collection
    {
        return $this->accessibilityTestOrganisations;
    }

    /**
     * @param Organisation[]|Collection $accessibilityTestOrganisations
     */
    public function setAccessibilityTestOrganisations($accessibilityTestOrganisations): void
    {
        $this->accessibilityTestOrganisations = $accessibilityTestOrganisations;
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

    /**
     * @return string|null
     */
    public function getAccessibilityTestOrganisationOthers(): ?string
    {
        return $this->accessibilityTestOrganisationOthers;
    }

    /**
     * @param string|null $accessibilityTestOrganisationOthers
     */
    public function setAccessibilityTestOrganisationOthers(?string $accessibilityTestOrganisationOthers): void
    {
        $this->accessibilityTestOrganisationOthers = $accessibilityTestOrganisationOthers;
    }

    /**
     * @return bool
     */
    public function isAccessibilitySelfTesting(): bool
    {
        return (bool) $this->accessibilitySelfTesting;
    }

    /**
     * @param bool $accessibilitySelfTesting
     */
    public function setAccessibilitySelfTesting(bool $accessibilitySelfTesting): void
    {
        $this->accessibilitySelfTesting = $accessibilitySelfTesting;
    }

    /**
     * @return int|null
     */
    public function getAccessibilityTestResultType(): ?int
    {
        return $this->accessibilityTestResultType;
    }

    /**
     * @param int|null $accessibilityTestResultType
     */
    public function setAccessibilityTestResultType(?int $accessibilityTestResultType): void
    {
        $this->accessibilityTestResultType = $accessibilityTestResultType;
    }

    /**
     * Add accessibility document
     *
     * @param ApplicationAccessibilityDocument $accessibilityDocument
     *
     * @return self
     */
    public function addAccessibilityDocument(ApplicationAccessibilityDocument $accessibilityDocument): self
    {
        if (!$this->accessibilityDocuments->contains($accessibilityDocument)) {
            $this->accessibilityDocuments->add($accessibilityDocument);
            $accessibilityDocument->setApplication($this);
        }
        return $this;
    }

    /**
     * Remove accessibility document
     *
     * @param ApplicationAccessibilityDocument $accessibilityDocument
     */
    public function removeAccessibilityDocument(ApplicationAccessibilityDocument $accessibilityDocument): void
    {
        if ($this->accessibilityDocuments->contains($accessibilityDocument)) {
            $this->accessibilityDocuments->removeElement($accessibilityDocument);
            $accessibilityDocument->setApplication(null);
        }
    }

    /**
     * Get accessibility documents
     *
     * @return Collection
     */
    public function getDocuments(): Collection
    {
        return $this->accessibilityDocuments;
    }

    /**
     * @param ApplicationAccessibilityDocument[]|Collection $accessibilityDocuments
     */
    public function setAccessibilityDocuments($accessibilityDocuments): void
    {
        $this->accessibilityDocuments = $accessibilityDocuments;
    }

    /**
     * Get application documents
     *
     * @return Collection
     */
    public function getAccessibilityDocuments(): Collection
    {
        return $this->accessibilityDocuments;
    }

    /**
     * Hook on persist and update operations.
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return ApplicationAccessibilityDocument[]|array Invalid accessibilityDocuments (without file reference)
     */
    public function cleanAccessibilityDocuments(): array
    {
        $removeDocuments = [];
        foreach ($this->accessibilityDocuments as $accessibilityDocument) {
            /** @var ApplicationAccessibilityDocument $accessibilityDocument */
            if (0 < (int)$accessibilityDocument->getId() && null === $accessibilityDocument->getLocalName()) {
                $removeDocuments[] = $accessibilityDocument;
            }
        }
        foreach ($removeDocuments as $accessibilityDocument) {
            $this->removeAccessibilityDocument($accessibilityDocument);
        }
        return $removeDocuments;
    }
}
