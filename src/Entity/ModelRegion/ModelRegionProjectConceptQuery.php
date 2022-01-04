<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\ModelRegion;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\SortableEntityInterface;
use App\Entity\Base\SortableEntityTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ModelRegionProjectConceptQuery
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_model_region_project_concept_query")
 */
class ModelRegionProjectConceptQuery extends BaseEntity implements SortableEntityInterface
{
    use SortableEntityTrait;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var ModelRegionProject|null
     * @ORM\ManyToOne(targetEntity="App\Entity\ModelRegion\ModelRegionProject", inversedBy="conceptQueries")
     * @ORM\JoinColumn(name="model_region_project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $modelRegionProject;

    /**
     * @var ConceptQueryType|null
     * @ORM\ManyToOne(targetEntity="App\Entity\ModelRegion\ConceptQueryType", cascade={"persist"})
     * @ORM\JoinColumn(name="concept_query_type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $conceptQueryType;

    /**
     * @return ModelRegionProject|null
     */
    public function getModelRegionProject(): ?ModelRegionProject
    {
        return $this->modelRegionProject;
    }

    /**
     * @param ModelRegionProject|null $modelRegionProject
     */
    public function setModelRegionProject(?ModelRegionProject $modelRegionProject): void
    {
        $this->modelRegionProject = $modelRegionProject;
    }

    /**
     * @return ConceptQueryType|null
     */
    public function getConceptQueryType(): ?ConceptQueryType
    {
        return $this->conceptQueryType;
    }

    /**
     * @param ConceptQueryType|null $conceptQueryType
     */
    public function setConceptQueryType(?ConceptQueryType $conceptQueryType): void
    {
        $this->conceptQueryType = $conceptQueryType;
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

    public function __toString(): string
    {
        if (null !== $queryType = $this->getConceptQueryType()) {
            return $queryType . '';
        }
        return $this->getDescription() ?: 'n/a';
    }
}
