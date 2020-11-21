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
use App\Entity\Base\HasDocumentsEntityInterface;
use App\Entity\Base\SluggableEntityTrait;
use App\Entity\Base\SluggableInterface;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ModelRegionProject
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_model_region_project")
 * @ORM\HasLifecycleCallbacks
 */
class ModelRegionProject extends BaseNamedEntity implements SluggableInterface, HasDocumentsEntityInterface, HasMetaDateEntityInterface
{
    use ImportTrait;
    use SluggableEntityTrait;

    /**
     * @var ModelRegion[]|Collection
     * @ORM\ManyToMany(targetEntity="ModelRegion", mappedBy="modelRegionProjects")
     */
    private $modelRegions;

    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = '';

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="project_start_at")
     */
    protected $projectStartAt;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="project_end_at")
     */
    protected $projectEndAt;

    /**
     * Usp
     * @var string|null
     *
     * @ORM\Column(name="usp", type="text", nullable=true)
     */
    protected $usp = '';

    /**
     * Communes benefits
     * @var string|null
     *
     * @ORM\Column(name="communes_benefits", type="text", nullable=true)
     */
    protected $communesBenefits = '';

    /**
     * Transferable service
     * @var string|null
     *
     * @ORM\Column(name="transferable_service", type="text", nullable=true)
     */
    protected $transferableService = '';

    /**
     * Transferable start
     *
     * @var string|null
     *
     * @ORM\Column(name="transferable_start", type="text", nullable=true)
     */
    protected $transferableStart = '';

    /**
     * @var Organisation[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Organisation", inversedBy="modelRegionProjects")
     * @ORM\JoinTable(name="ozg_model_region_project_organisation",
     *     joinColumns={
     *     @ORM\JoinColumn(name="model_region_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     *   }
     * )
     */
    private $organisations;

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Solution", inversedBy="modelRegionProjects")
     * @ORM\JoinTable(name="ozg_model_region_project_solutions",
     *     joinColumns={
     *     @ORM\JoinColumn(name="model_region_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   }
     * )
     */
    private $solutions;

    /**
     * @var ModelRegionProjectDocument[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\ModelRegionProjectDocument", mappedBy="modelRegionProject", cascade={"persist", "remove"})
     */
    private $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->modelRegions = new ArrayCollection();
        $this->organisations = new ArrayCollection();
        $this->solutions = new ArrayCollection();
    }

    /**
     * @param ModelRegion $modelRegion
     * @return self
     */
    public function addModelRegion($modelRegion): self
    {
        if (!$this->modelRegions->contains($modelRegion)) {
            $this->modelRegions->add($modelRegion);
            $modelRegion->addModelRegionProject($this);
        }

        return $this;
    }

    /**
     * @param ModelRegion $modelRegion
     * @return self
     */
    public function removeModelRegion($modelRegion): self
    {
        if ($this->modelRegions->contains($modelRegion)) {
            $this->modelRegions->removeElement($modelRegion);
            $modelRegion->removeModelRegionProject($this);
        }

        return $this;
    }

    /**
     * @return ModelRegion[]|Collection
     */
    public function getModelRegions()
    {
        return $this->modelRegions;
    }

    /**
     * @param ModelRegion[]|Collection $modelRegions
     */
    public function setModelRegions($modelRegions): void
    {
        $this->modelRegions = $modelRegions;
    }

    /**
     * @param Organisation $organisation
     * @return self
     */
    public function addOrganisation($organisation): self
    {
        if (!$this->organisations->contains($organisation)) {
            $this->organisations->add($organisation);
        }

        return $this;
    }

    /**
     * @param Organisation $organisation
     * @return self
     */
    public function removeOrganisation($organisation): self
    {
        if ($this->organisations->contains($organisation)) {
            $this->organisations->removeElement($organisation);
        }

        return $this;
    }

    /**
     * @return Organisation[]|Collection
     */
    public function getOrganisations()
    {
        return $this->organisations;
    }

    /**
     * @param Organisation[]|Collection $organisations
     */
    public function setOrganisations($organisations): void
    {
        $this->organisations = $organisations;
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
     * @return DateTime|null
     */
    public function getProjectStartAt(): ?DateTime
    {
        return $this->projectStartAt;
    }

    /**
     * @param DateTime|null $projectStartAt
     */
    public function setProjectStartAt(?DateTime $projectStartAt): void
    {
        $this->projectStartAt = $projectStartAt;
    }

    /**
     * @return DateTime|null
     */
    public function getProjectEndAt(): ?DateTime
    {
        return $this->projectEndAt;
    }

    /**
     * @param DateTime|null $projectEndAt
     */
    public function setProjectEndAt(?DateTime $projectEndAt): void
    {
        $this->projectEndAt = $projectEndAt;
    }

    /**
     * @return string|null
     */
    public function getUsp(): ?string
    {
        return $this->usp;
    }

    /**
     * @param string|null $usp
     */
    public function setUsp(?string $usp): void
    {
        $this->usp = $usp;
    }

    /**
     * @return string|null
     */
    public function getCommunesBenefits(): ?string
    {
        return $this->communesBenefits;
    }

    /**
     * @param string|null $communesBenefits
     */
    public function setCommunesBenefits(?string $communesBenefits): void
    {
        $this->communesBenefits = $communesBenefits;
    }

    /**
     * @return string|null
     */
    public function getTransferableService(): ?string
    {
        return $this->transferableService;
    }

    /**
     * @param string|null $transferableService
     */
    public function setTransferableService(?string $transferableService): void
    {
        $this->transferableService = $transferableService;
    }

    /**
     * @return string|null
     */
    public function getTransferableStart(): ?string
    {
        return $this->transferableStart;
    }

    /**
     * @param string|null $transferableStart
     */
    public function setTransferableStart(?string $transferableStart): void
    {
        $this->transferableStart = $transferableStart;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addModelRegionProject($this);
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
            $solution->removeModelRegionProject($this);
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

    /**
     * Add document
     *
     * @param ModelRegionProjectDocument $document
     *
     * @return self
     */
    public function addDocument(ModelRegionProjectDocument $document): self
    {
        $this->documents->add($document);
        $document->setModelRegionProject($this);
        return $this;
    }

    /**
     * Remove document
     *
     * @param ModelRegionProjectDocument $document
     */
    public function removeDocument(ModelRegionProjectDocument $document): void
    {
        $this->documents->removeElement($document);
        $document->setModelRegionProject(null);
    }

    /**
     * Get documents
     *
     * @return Collection
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @param ModelRegionProjectDocument[]|Collection $documents
     */
    public function setDocuments($documents): void
    {
        $this->documents = $documents;
    }

    /**
     * Hook on persist and update operations.
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return ModelRegionProjectDocument[]|array Invalid documents (without file reference)
     */
    public function cleanDocuments(): array
    {
        $removeDocuments = [];
        foreach ($this->documents as $document) {
            /** @var ModelRegionProjectDocument $document */
            if (0 < (int) $document->getId() && null === $document->getLocalName()) {
                $removeDocuments[] = $document;
            }
        }
        foreach ($removeDocuments as $document) {
            $this->removeDocument($document);
        }
        return $removeDocuments;
    }

}
