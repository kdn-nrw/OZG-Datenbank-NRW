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

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Base\SortableEntityInterface;
use App\Entity\Base\SortableEntityTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ConceptQueryType
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_model_region_concept_query_type")
 */
class ConceptQueryType extends BaseNamedEntity implements SortableEntityInterface
{
    use SortableEntityTrait;

    public const GROUP_1 = 1;
    public const GROUP_2 = 2;
    public const GROUP_3 = 3;
    public const GROUP_4 = 4;
    public const GROUP_5 = 5;

    public static $mapTypes = [
        self::GROUP_1 => 'app.concept_query_type.entity.query_group_choices.1',
        self::GROUP_2 => 'app.concept_query_type.entity.query_group_choices.2',
        self::GROUP_3 => 'app.concept_query_type.entity.query_group_choices.3',
        self::GROUP_4 => 'app.concept_query_type.entity.query_group_choices.4',
        self::GROUP_5 => 'app.concept_query_type.entity.query_group_choices.5',
    ];

    /**
     * The query group
     *
     * @var string
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $queryGroup = self::GROUP_1;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="placeholder", type="text", nullable=true)
     */
    protected $placeholder;

    /**
     * @return string
     */
    public function getQueryGroup(): string
    {
        return $this->queryGroup;
    }

    /**
     * @param string $queryGroup
     */
    public function setQueryGroup(string $queryGroup): void
    {
        $this->queryGroup = $queryGroup;
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
    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    /**
     * @param string|null $placeholder
     */
    public function setPlaceholder(?string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Returns the label key for the current data type
     *
     * @return string
     */
    public function getQueryGroupLabel(): string
    {
        return self::$mapTypes[$this->getQueryGroup()];
    }

    /**
     * @return string|null
     */
    public function getLabelKey(): ?string
    {
        return $this->getQueryGroupLabel();
    }
}
