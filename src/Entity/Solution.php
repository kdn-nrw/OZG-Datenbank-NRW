<?php

namespace App\Entity;

use App\Entity\Base\BaseBlamableEntity;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\NamedEntityTrait;
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
class Solution extends BaseBlamableEntity implements NamedEntityInterface
{
    const COMMUNE_TYPE_ALL = 'all';
    const COMMUNE_TYPE_SELECTED = 'selected';

    use NamedEntityTrait;
    use HideableEntityTrait;
    use UrlTrait;

    /**
     * @var ServiceProvider
     * @ORM\ManyToOne(targetEntity="ServiceProvider", inversedBy="solutions", cascade={"persist"})
     */
    private $serviceProvider;

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
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * @var ServiceSolution[]|Collection
     * @ORM\OneToMany(targetEntity="ServiceSolution", mappedBy="solution", cascade={"all"}, orphanRemoval=true)
     */
    private $serviceSolutions;

    /**
     * Contact
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $contact;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="SpecializedProcedure", inversedBy="solutions")
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
     * @ORM\ManyToMany(targetEntity="Portal", inversedBy="solutions")
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
     * @ORM\ManyToMany(targetEntity="Commune", inversedBy="solutions")
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
     * @var FormServer[]|Collection
     * @ORM\ManyToMany(targetEntity="FormServer", mappedBy="solutions")
     */
    private $formServers;

    /**
     * @var PaymentType[]|Collection
     * @ORM\ManyToMany(targetEntity="PaymentType", mappedBy="solutions")
     */
    private $paymentTypes;

    /**
     * @var ImplementationProject[]|Collection
     * @ORM\ManyToMany(targetEntity="ImplementationProject", mappedBy="solutions")
     */
    private $implementationProjects;
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
     * @ORM\ManyToMany(targetEntity="Contact", inversedBy="solutions")
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
        $this->serviceSolutions = new ArrayCollection();
        $this->specializedProcedures = new ArrayCollection();
        $this->portals = new ArrayCollection();
        $this->communes = new ArrayCollection();
        $this->analogServices = new ArrayCollection();
        $this->authentications = new ArrayCollection();
        $this->formServers = new ArrayCollection();
        $this->openDataItems = new ArrayCollection();
        $this->paymentTypes = new ArrayCollection();
        $this->implementationProjects = new ArrayCollection();
        $this->solutionContacts = new ArrayCollection();
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
     * @return ServiceProvider
     */
    public function getServiceProvider()
    {
        return $this->serviceProvider;
    }

    /**
     * @param ServiceProvider $serviceProvider
     */
    public function setServiceProvider($serviceProvider): void
    {
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * @param ServiceSolution $serviceSolution
     * @return self
     */
    public function addServiceSolution($serviceSolution)
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
    public function removeServiceSolution($serviceSolution)
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
    public function getMaturity()
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
    protected function updateMaturity()
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
        if (count($groupedMaturities) == 1) {
            $maturity = current($groupedMaturities);
        }
        $this->maturity = $maturity;
    }

    /**
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function addSpecializedProcedure($specializedProcedure)
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
    public function removeSpecializedProcedure($specializedProcedure)
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
     * @param Portal $portal
     * @return self
     */
    public function addPortal($portal)
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
    public function removePortal($portal)
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
    public function addCommune($commune)
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
    public function removeCommune($commune)
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
    public function addAuthentication($authentication)
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
    public function removeAuthentication($authentication)
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
    public function addAnalogService($analogService)
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
    public function removeAnalogService($analogService)
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
    public function addOpenDataItem($openData)
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
    public function removeOpenDataItem($openData)
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
     * @param FormServer $formServer
     * @return self
     */
    public function addFormServer($formServer)
    {
        if (!$this->formServers->contains($formServer)) {
            $this->formServers->add($formServer);
            $formServer->addSolution($this);
        }

        return $this;
    }

    /**
     * @param FormServer $formServer
     * @return self
     */
    public function removeFormServer($formServer)
    {
        if ($this->formServers->contains($formServer)) {
            $this->formServers->removeElement($formServer);
            $formServer->removeSolution($this);
        }

        return $this;
    }

    /**
     * @return FormServer[]|Collection
     */
    public function getFormServers()
    {
        return $this->formServers;
    }

    /**
     * @param FormServer[]|Collection $formServers
     */
    public function setFormServers($formServers): void
    {
        $this->formServers = $formServers;
    }

    /**
     * @param PaymentType $paymentType
     * @return self
     */
    public function addPaymentType($paymentType)
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
    public function removePaymentType($paymentType)
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
    public function addImplementationProject($implementationProject)
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
    public function removeImplementationProject($implementationProject)
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

    public function getDisplayName()
    {
        $label = $this->getName();
        if (empty($label)) {
            $label = $this->getId();
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
    public function addSolutionContact($solutionContact)
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
    public function removeSolutionContact($solutionContact)
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
