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
use App\Entity\Base\SluggableEntityTrait;
use App\Entity\Base\SluggableInterface;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class solution
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_solution")
 * @ORM\HasLifecycleCallbacks
 */
class Solution extends BaseBlamableEntity implements NamedEntityInterface, ImportEntityInterface, SluggableInterface, HasMetaDateEntityInterface
{
    public const COMMUNE_TYPE_ALL = 'all';
    public const COMMUNE_TYPE_SELECTED = 'selected';

    use ContactTextTrait;
    use HideableEntityTrait;
    use ImportTrait;
    use SluggableEntityTrait;
    use UrlTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    protected $name;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\ServiceProvider", inversedBy="solutions")
     * @ORM\JoinTable(name="ozg_solution_service_provider",
     *     joinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
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
     * @ORM\Column(name="custom_provider", type="text", nullable=true)
     */
    private $customProvider = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Status
     * @var Status|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * @var ServiceSolution[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\ServiceSolution", mappedBy="solution", cascade={"all"}, orphanRemoval=true)
     */
    private $serviceSolutions;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\SpecializedProcedure", inversedBy="solutions")
     * @ORM\JoinTable(name="ozg_solutions_specialized_procedures",
     *     joinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="specialized_procedure_id", referencedColumnName="id")
     *   }
     * )
     */
    private $specializedProcedures;

    /**
     * @var Portal[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Portal", inversedBy="solutions")
     * @ORM\JoinTable(name="ozg_solutions_portals",
     *     joinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="portal_id", referencedColumnName="id")
     *   }
     * )
     */
    private $portals;

    /**
     * Commune selection type
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $communeType = self::COMMUNE_TYPE_SELECTED;

    /**
     * @var Commune[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Commune", inversedBy="solutions")
     * @ORM\JoinTable(name="ozg_solutions_communes",
     *     joinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id")
     *   }
     * )
     */
    private $communes;

    /**
     * @var Authentication[]|Collection
     * @ORM\ManyToMany(targetEntity="Authentication", inversedBy="solutions")
     * @ORM\JoinTable(name="ozg_solutions_authentications",
     *     joinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="authentication_id", referencedColumnName="id")
     *   }
     * )
     */
    private $authentications;

    /**
     * @var AnalogService[]|Collection
     * @ORM\ManyToMany(targetEntity="AnalogService", inversedBy="solutions")
     * @ORM\JoinTable(name="ozg_solution_analog_service",
     *     joinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="analog_service_id", referencedColumnName="id")
     *   }
     * )
     */
    private $analogServices;

    /**
     * @var OpenData[]|Collection
     * @ORM\ManyToMany(targetEntity="OpenData", inversedBy="solutions")
     * @ORM\JoinTable(name="ozg_solution_open_data",
     *     joinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="open_data_id", referencedColumnName="id")
     *   }
     * )
     */
    private $openDataItems;

    /**
     * @var Maturity|null
     *
     * @ORM\ManyToOne(targetEntity="Maturity", cascade={"persist"})
     * @ORM\JoinColumn(name="maturity_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $maturity;

    /**
     * @var FormServerSolution[]|Collection
     * @ORM\OneToMany(targetEntity="FormServerSolution", mappedBy="solution", cascade={"all"}, orphanRemoval=true)
     */
    private $formServerSolutions;

    /**
     * @var PaymentType[]|Collection
     * @ORM\ManyToMany(targetEntity="PaymentType", mappedBy="solutions")
     */
    private $paymentTypes;

    /**
     * @var ImplementationProject[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\ImplementationProject", mappedBy="solutions")
     */
    private $implementationProjects;

    /**
     * @var ModelRegionProject[]|Collection
     * @ORM\ManyToMany(targetEntity="ModelRegionProject", mappedBy="solutions")
     */
    private $modelRegionProjects;

    /**
     * Solution is published
     *
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean")
     */
    protected $isPublished = false;

    /**
     * @var Contact[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Contact", inversedBy="solutions")
     * @ORM\JoinTable(name="ozg_solution_contact",
     *     joinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *   }
     * )
     */
    private $solutionContacts;

    public function __construct()
    {
        $this->analogServices = new ArrayCollection();
        $this->authentications = new ArrayCollection();
        $this->communes = new ArrayCollection();
        $this->formServerSolutions = new ArrayCollection();
        $this->implementationProjects = new ArrayCollection();
        $this->modelRegionProjects = new ArrayCollection();
        $this->openDataItems = new ArrayCollection();
        $this->paymentTypes = new ArrayCollection();
        $this->portals = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
        $this->serviceSolutions = new ArrayCollection();
        $this->solutionContacts = new ArrayCollection();
        $this->specializedProcedures = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string|null $name
     * @return self
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
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
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function addServiceProvider($serviceProvider): self
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
            $serviceProvider->addSolution($this);
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
            $serviceProvider->removeSolution($this);
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
     * @param ServiceSolution $serviceSolution
     * @return self
     */
    public function addServiceSolution($serviceSolution): self
    {
        if (!$this->serviceSolutions->contains($serviceSolution)) {
            $this->serviceSolutions->add($serviceSolution);
            $serviceSolution->setSolution($this);
            // update solution maturity
            $this->updateMaturity();
        }

        return $this;
    }

    /**
     * @param ServiceSolution $serviceSolution
     * @return self
     */
    public function removeServiceSolution($serviceSolution): self
    {
        if ($this->serviceSolutions->contains($serviceSolution)) {
            $this->serviceSolutions->removeElement($serviceSolution);
        }

        return $this;
    }

    /**
     * @return ServiceSolution[]|Collection
     */
    public function getServiceSolutions()
    {
        return $this->serviceSolutions;
    }

    /**
     * @param ServiceSolution[]|Collection $serviceSolutions
     */
    public function setServiceSolutions($serviceSolutions): void
    {
        $this->serviceSolutions = $serviceSolutions;
    }

    /**
     * Returns the published service solutions (if this solution is published, all service solutions are returned)
     *
     * @return ServiceSolution[]|Collection
     */
    public function getPublishedServiceSolutions()
    {
        if ($this->isPublished()) {
            return $this->getServiceSolutions();
        }
        return new ArrayCollection();
    }

    /**
     * @return ServiceSystem[]|Collection
     */
    public function getServiceSystems()
    {
        $serviceSystems = new ArrayCollection();
        $serviceSolutions = $this->getServiceSolutions();
        foreach ($serviceSolutions as $serviceSolution) {
            if (null !== $service = $serviceSolution->getService()) {
                $serviceSystem = $service->getServiceSystem();
                if (null !== $serviceSystem && !$serviceSystems->contains($serviceSystem)) {
                    $serviceSystems->add($serviceSystem);
                }
            }
        }
        return $serviceSystems;
    }

    /**
     * @return Maturity|null
     */
    public function getMaturity(): ?Maturity
    {
        $this->updateMaturity();
        return $this->maturity;
    }

    /**
     * @param Maturity|null $maturity
     */
    public function setMaturity($maturity): void
    {
        $this->maturity = $maturity;
    }

    /**
     * Update the solution maturity based on the service solution maturities
     */
    protected function updateMaturity(): void
    {
        $maturity = $this->maturity;
        $groupedMaturities = [];
        /** @var Maturity $maturity */
        $serviceSolutions = $this->getServiceSolutions();
        foreach ($serviceSolutions as $serviceSolution) {
            if (null !== $ssMaturity = $serviceSolution->getMaturity()) {
                if (!isset($groupedMaturities[$ssMaturity->getId()])) {
                    $groupedMaturities[$ssMaturity->getId()] = $ssMaturity;
                }
                if (null === $maturity
                    || (is_numeric($ssMaturity->getName()) && (int)$maturity->getName() > (int)$ssMaturity->getName())) {
                    $maturity = $ssMaturity;
                }
            }
        }
        if (count($groupedMaturities) === 1) {
            $maturity = current($groupedMaturities);
        }
        $this->maturity = $maturity;
    }

    /**
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function addSpecializedProcedure($specializedProcedure): self
    {
        if (!$this->specializedProcedures->contains($specializedProcedure)) {
            $this->specializedProcedures->add($specializedProcedure);
            $specializedProcedure->addSolution($this);
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
            $specializedProcedure->removeSolution($this);
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
     * @return string|null
     */
    public function getCustomProvider(): ?string
    {
        return $this->customProvider;
    }

    /**
     * @param string|null $customProvider
     */
    public function setCustomProvider(?string $customProvider): void
    {
        $this->customProvider = $customProvider;
    }

    /**
     * @param Portal $portal
     * @return self
     */
    public function addPortal($portal): self
    {
        if (!$this->portals->contains($portal)) {
            $this->portals->add($portal);
            $portal->addSolution($this);
        }

        return $this;
    }

    /**
     * @param Portal $portal
     * @return self
     */
    public function removePortal($portal): self
    {
        if ($this->portals->contains($portal)) {
            $this->portals->removeElement($portal);
            $portal->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return Portal[]|Collection
     */
    public function getPortals()
    {
        return $this->portals;
    }

    /**
     * @param Portal[]|Collection $portals
     */
    public function setPortals($portals): void
    {
        $this->portals = $portals;
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function addCommune($commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
            $commune->addSolution($this);
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
            $commune->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCommuneType(): string
    {
        return empty($this->communeType) ? self::COMMUNE_TYPE_SELECTED : $this->communeType;
    }

    /**
     * @param string|null $communeType
     */
    public function setCommuneType(?string $communeType): void
    {
        $this->communeType = $communeType;
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
     * @param Authentication $authentication
     * @return self
     */
    public function addAuthentication($authentication): self
    {
        if (!$this->authentications->contains($authentication)) {
            $this->authentications->add($authentication);
            $authentication->addSolution($this);
        }

        return $this;
    }

    /**
     * @param Authentication $authentication
     * @return self
     */
    public function removeAuthentication($authentication): self
    {
        if ($this->authentications->contains($authentication)) {
            $this->authentications->removeElement($authentication);
            $authentication->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return Authentication[]|Collection
     */
    public function getAuthentications()
    {
        return $this->authentications;
    }

    /**
     * @param Authentication[]|Collection $authentications
     */
    public function setAuthentications($authentications): void
    {
        $this->authentications = $authentications;
    }

    /**
     * @param AnalogService $analogService
     * @return self
     */
    public function addAnalogService($analogService): self
    {
        if (!$this->analogServices->contains($analogService)) {
            $this->analogServices->add($analogService);
            $analogService->addSolution($this);
        }

        return $this;
    }

    /**
     * @param AnalogService $analogService
     * @return self
     */
    public function removeAnalogService($analogService): self
    {
        if ($this->analogServices->contains($analogService)) {
            $this->analogServices->removeElement($analogService);
            $analogService->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return AnalogService[]|Collection
     */
    public function getAnalogServices()
    {
        return $this->analogServices;
    }

    /**
     * @param AnalogService[]|Collection $analogServices
     */
    public function setAnalogServices($analogServices): void
    {
        $this->analogServices = $analogServices;
    }

    /**
     * @param OpenData $openData
     * @return self
     */
    public function addOpenDataItem($openData): self
    {
        if (!$this->openDataItems->contains($openData)) {
            $this->openDataItems->add($openData);
            $openData->addSolution($this);
        }

        return $this;
    }

    /**
     * @param OpenData $openData
     * @return self
     */
    public function removeOpenDataItem($openData): self
    {
        if ($this->openDataItems->contains($openData)) {
            $this->openDataItems->removeElement($openData);
            $openData->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return OpenData[]|Collection
     */
    public function getOpenDataItems()
    {
        return $this->openDataItems;
    }

    /**
     * @param OpenData[]|Collection $openDataItems
     */
    public function setOpenDataItems($openDataItems): void
    {
        $this->openDataItems = $openDataItems;
    }

    /**
     * @param FormServerSolution $formServerSolution
     * @return self
     */
    public function addFormServerSolution(FormServerSolution $formServerSolution): self
    {
        if (!$this->formServerSolutions->contains($formServerSolution)) {
            $this->formServerSolutions->add($formServerSolution);
            $formServerSolution->setSolution($this);
        }

        return $this;
    }

    /**
     * @param FormServerSolution $formServerSolution
     * @return self
     */
    public function removeFormServerSolution($formServerSolution): self
    {
        if ($this->formServerSolutions->contains($formServerSolution)) {
            $this->formServerSolutions->removeElement($formServerSolution);
        }

        return $this;
    }

    /**
     * @return FormServerSolution[]|Collection
     */
    public function getFormServerSolutions()
    {
        return $this->formServerSolutions;
    }

    /**
     * @param FormServerSolution[]|Collection $formServerSolutions
     */
    public function setFormServerSolutions($formServerSolutions): void
    {
        $this->formServerSolutions = $formServerSolutions;
    }

    /**
     * @param PaymentType $paymentType
     * @return self
     */
    public function addPaymentType($paymentType): self
    {
        if (!$this->paymentTypes->contains($paymentType)) {
            $this->paymentTypes->add($paymentType);
            $paymentType->addSolution($this);
        }

        return $this;
    }

    /**
     * @param PaymentType $paymentType
     * @return self
     */
    public function removePaymentType($paymentType): self
    {
        if ($this->paymentTypes->contains($paymentType)) {
            $this->paymentTypes->removeElement($paymentType);
            $paymentType->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return PaymentType[]|Collection
     */
    public function getPaymentTypes()
    {
        return $this->paymentTypes;
    }

    /**
     * @param PaymentType[]|Collection $paymentTypes
     */
    public function setPaymentTypes($paymentTypes): void
    {
        $this->paymentTypes = $paymentTypes;
    }

    /**
     * @param ImplementationProject $implementationProject
     * @return self
     */
    public function addImplementationProject($implementationProject): self
    {
        if (!$this->implementationProjects->contains($implementationProject)) {
            $this->implementationProjects->add($implementationProject);
            $implementationProject->addSolution($this);
        }

        return $this;
    }

    /**
     * @param ImplementationProject $implementationProject
     * @return self
     */
    public function removeImplementationProject($implementationProject): self
    {
        if ($this->implementationProjects->contains($implementationProject)) {
            $this->implementationProjects->removeElement($implementationProject);
            $implementationProject->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return ImplementationProject[]|Collection
     */
    public function getImplementationProjects()
    {
        return $this->implementationProjects;
    }

    /**
     * @param ImplementationProject[]|Collection $implementationProjects
     */
    public function setImplementationProjects($implementationProjects): void
    {
        $this->implementationProjects = $implementationProjects;
    }

    /**
     * @param ModelRegionProject $modelRegionProject
     * @return self
     */
    public function addModelRegionProject($modelRegionProject): self
    {
        if (!$this->modelRegionProjects->contains($modelRegionProject)) {
            $this->modelRegionProjects->add($modelRegionProject);
            $modelRegionProject->addSolution($this);
        }

        return $this;
    }

    /**
     * @param ModelRegionProject $modelRegionProject
     * @return self
     */
    public function removeModelRegionProject($modelRegionProject): self
    {
        if ($this->modelRegionProjects->contains($modelRegionProject)) {
            $this->modelRegionProjects->removeElement($modelRegionProject);
            $modelRegionProject->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return ModelRegionProject[]|Collection
     */
    public function getModelRegionProjects()
    {
        return $this->modelRegionProjects;
    }

    /**
     * @param ModelRegionProject[]|Collection $modelRegionProjects
     */
    public function setModelRegionProjects($modelRegionProjects): void
    {
        $this->modelRegionProjects = $modelRegionProjects;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        $label = (string) $this->getName();
        if (empty($label)) {
            $label = (string) $this->getId();
            if (empty($label)) {
                $label = 'Neue Lösung';
            } else {
                $label = 'ID:' . $label;
            }
        }
        return $label;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return (bool) $this->isPublished;
    }

    /**
     * @param bool $isPublished
     */
    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @param Contact $solutionContact
     * @return self
     */
    public function addSolutionContact($solutionContact): self
    {
        if (!$this->solutionContacts->contains($solutionContact)) {
            $this->solutionContacts->add($solutionContact);
            $solutionContact->addSolution($this);
        }

        return $this;
    }

    /**
     * @param Contact $solutionContact
     * @return self
     */
    public function removeSolutionContact($solutionContact): self
    {
        if ($this->solutionContacts->contains($solutionContact)) {
            $this->solutionContacts->removeElement($solutionContact);
            $solutionContact->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getSolutionContacts()
    {
        return $this->solutionContacts;
    }

    /**
     * @param Contact[]|Collection $solutionContacts
     */
    public function setSolutionContacts($solutionContacts): void
    {
        $this->solutionContacts = $solutionContacts;
    }

    /**
     * Returns the subjects of the referenced service systems
     *
     * @return Jurisdiction[]|array
     */
    public function getJurisdictions(): array
    {
        $entities = [];
        $serviceSystems = $this->getServiceSystems();
        foreach ($serviceSystems as $serviceSystem) {
            $ssJurisdictions = $serviceSystem->getJurisdictions();
            foreach ($ssJurisdictions as $jurisdiction) {
                if (!isset($entities[$jurisdiction->getId()])) {
                    $entities[$jurisdiction->getId()] = $jurisdiction;
                }
            }
        }
        return $entities;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    /**
     * Hook on pre-update operations.
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->updateMaturity();
    }

    /**
     * Hook on pre-persist operations.
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->updateMaturity();
    }
}
