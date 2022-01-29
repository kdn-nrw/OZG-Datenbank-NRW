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
    public const GROUP_21 = 21;
    public const GROUP_22 = 22;
    public const GROUP_31 = 31;
    public const GROUP_32 = 32;
    public const GROUP_33 = 33;
    public const GROUP_34 = 34;
    public const GROUP_35 = 35;
    public const GROUP_41 = 41;

    public const GROUP_TYPE_LABEL_PREFIX = 'app.concept_query_type.entity.query_group_choices.';
    public const GROUP_TYPE_FORM_LABEL_PREFIX = 'app.concept_query_type.entity.query_group_form_choices.';

    public static $groupTypeKey = [
        self::GROUP_1,
        self::GROUP_2,
        self::GROUP_3,
        self::GROUP_4,
        self::GROUP_5,
        self::GROUP_21,
        self::GROUP_22,
        self::GROUP_31,
        self::GROUP_32,
        self::GROUP_33,
        self::GROUP_34,
        self::GROUP_35,
        self::GROUP_41,
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
     * Choices for dropdown in from
     *
     * @var string|null
     *
     * @ORM\Column(name="choices_text", type="text", nullable=true)
     */
    private $choicesText = '';

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
     * @return string|null
     */
    public function getChoicesText(): ?string
    {
        return $this->choicesText;
    }

    /**
     * @param string|null $choicesText
     */
    public function setChoicesText(?string $choicesText): void
    {
        $this->choicesText = $choicesText;
    }

    /**
     * Returns the label key for the current data type
     *
     * @return string
     */
    public function getQueryGroupLabel(): string
    {
        return self::GROUP_TYPE_LABEL_PREFIX . $this->getQueryGroup();
    }

    /**
     * @return string|null
     */
    public function getLabelKey(): ?string
    {
        return $this->getQueryGroupLabel();
    }

    /**
     * Returns an array with a mapping of all available type keys and their labels
     * @param bool $useFormLabels
     * @return array
     */
    public static function getTypeLabelMap($useFormLabels = false) {
        $labelPrefix = $useFormLabels ? self::GROUP_TYPE_FORM_LABEL_PREFIX : self::GROUP_TYPE_LABEL_PREFIX;
        $mapTypes = [];
        foreach (self::$groupTypeKey as $key) {
            $mapTypes[$key] = $labelPrefix . $key;
        }
        return $mapTypes;
    }
}
