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

use App\Entity\Base\BaseNamedEntity;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class EFile
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_efile")
 * @ORM\HasLifecycleCallbacks
 */
class EFile extends BaseNamedEntity implements ImportEntityInterface
{
    use UrlTrait;
    use ImportTrait;

    /**
     * @var ServiceProvider
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\ServiceProvider", cascade={"persist"})
     */
    private $serviceProvider;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\SpecializedProcedure")
     * @ORM\JoinTable(name="ozg_efile_specialized_procedures",
     *     joinColumns={
     *     @ORM\JoinColumn(name="efile_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="specialized_procedure_id", referencedColumnName="id")
     *   }
     * )
     */
    private $specializedProcedures;

    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Status
     * @var EFileStatus|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EFileStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * Leading system
     *
     * @var SpecializedProcedure|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\SpecializedProcedure")
     * @ORM\JoinColumn(name="leading_system_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $leadingSystem;

    /**
     * @var EFileStorageType[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\EFileStorageType", inversedBy="eFiles")
     * @ORM\JoinTable(name="ozg_efile_storage_type_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="efile_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="storage_type_id", referencedColumnName="id")
     *   }
     * )
     */
    private $storageTypes;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\SpecializedProcedure")
     * @ORM\JoinTable(name="ozg_efile_software_modules",
     *     joinColumns={
     *     @ORM\JoinColumn(name="efile_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="specialized_procedure_id", referencedColumnName="id")
     *   }
     * )
     */
    private $softwareModules;

    /**
     * Notes
     *
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes = '';

    /**
     * Economic viability assessment done
     *
     * @var bool
     *
     * @ORM\Column(name="has_economic_viability_assessment", type="boolean", nullable=true)
     */
    protected $hasEconomicViabilityAssessment = false;

    /**
     * @var string|null
     * @ORM\Column(name="sum_investments", type="decimal", precision=12, scale=2, nullable=true)
     */
    protected $sumInvestments;

    /**
     * @var string|null
     * @ORM\Column(name="follow_up_costs", type="decimal", precision=12, scale=2, nullable=true)
     */
    protected $followUpCosts;

    /**
     * Saving potential notes
     *
     * @var string|null
     *
     * @ORM\Column(name="saving_potential_notes", type="text", nullable=true)
     */
    private $savingPotentialNotes = '';

    public function __construct()
    {
        $this->softwareModules = new ArrayCollection();
        $this->specializedProcedures = new ArrayCollection();
        $this->storageTypes = new ArrayCollection();
    }

    /**
     * @return ServiceProvider|null
     */
    public function getServiceProvider(): ?ServiceProvider
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
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function addSpecializedProcedure($specializedProcedure): self
    {
        if (!$this->specializedProcedures->contains($specializedProcedure)) {
            $this->specializedProcedures->add($specializedProcedure);
            //$specializedProcedure->addManufacturer($this);
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
            //$specializedProcedure->removeManufacturer($this);
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
     * @return EFileStatus|null
     */
    public function getStatus(): ?EFileStatus
    {
        return $this->status;
    }

    /**
     * @param EFileStatus|null $status
     */
    public function setStatus(?EFileStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return SpecializedProcedure|null
     */
    public function getLeadingSystem(): ?SpecializedProcedure
    {
        return $this->leadingSystem;
    }

    /**
     * @param SpecializedProcedure|null $leadingSystem
     */
    public function setLeadingSystem(?SpecializedProcedure $leadingSystem): void
    {
        $this->leadingSystem = $leadingSystem;
    }

    /**
     * @param EFileStorageType $storageType
     * @return self
     */
    public function addStorageType($storageType): self
    {
        if (!$this->storageTypes->contains($storageType)) {
            $this->storageTypes->add($storageType);
            $storageType->addEFile($this);
        }

        return $this;
    }

    /**
     * @param EFileStorageType $storageType
     * @return self
     */
    public function removeStorageType($storageType): self
    {
        if ($this->storageTypes->contains($storageType)) {
            $this->storageTypes->removeElement($storageType);
            $storageType->removeEFile($this);
        }

        return $this;
    }

    /**
     * @return EFileStorageType[]|Collection
     */
    public function getStorageTypes()
    {
        return $this->storageTypes;
    }

    /**
     * @param EFileStorageType[]|Collection $storageTypes
     */
    public function setStorageTypes($storageTypes): void
    {
        $this->storageTypes = $storageTypes;
    }

    /**
     * @param SpecializedProcedure $softwareModule
     * @return self
     */
    public function addSoftwareModule($softwareModule): self
    {
        if (!$this->softwareModules->contains($softwareModule)) {
            $this->softwareModules->add($softwareModule);
        }

        return $this;
    }

    /**
     * @param SpecializedProcedure $softwareModule
     * @return self
     */
    public function removeSoftwareModule($softwareModule): self
    {
        if ($this->softwareModules->contains($softwareModule)) {
            $this->softwareModules->removeElement($softwareModule);
        }

        return $this;
    }

    /**
     * @return SpecializedProcedure[]|Collection
     */
    public function getSoftwareModules()
    {
        return $this->softwareModules;
    }

    /**
     * @param SpecializedProcedure[]|Collection $specializedProcedures
     */
    public function setSoftwareModules($specializedProcedures): void
    {
        $this->softwareModules = $specializedProcedures;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @return bool
     */
    public function isHasEconomicViabilityAssessment(): bool
    {
        return $this->hasEconomicViabilityAssessment;
    }

    /**
     * @param bool $hasEconomicViabilityAssessment
     */
    public function setHasEconomicViabilityAssessment(bool $hasEconomicViabilityAssessment): void
    {
        $this->hasEconomicViabilityAssessment = $hasEconomicViabilityAssessment;
    }

    /**
     * @return string|null
     */
    public function getSumInvestments(): ?string
    {
        return $this->sumInvestments;
    }

    /**
     * @param string|null $sumInvestments
     */
    public function setSumInvestments(?string $sumInvestments): void
    {
        $this->sumInvestments = $sumInvestments;
    }

    /**
     * @return string|null
     */
    public function getFollowUpCosts(): ?string
    {
        return $this->followUpCosts;
    }

    /**
     * @param string|null $followUpCosts
     */
    public function setFollowUpCosts(?string $followUpCosts): void
    {
        $this->followUpCosts = $followUpCosts;
    }

    /**
     * @return string|null
     */
    public function getSavingPotentialNotes(): ?string
    {
        return $this->savingPotentialNotes;
    }

    /**
     * @param string|null $savingPotentialNotes
     */
    public function setSavingPotentialNotes(?string $savingPotentialNotes): void
    {
        $this->savingPotentialNotes = $savingPotentialNotes;
    }

}
