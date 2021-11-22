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

namespace App\Entity\MetaData;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class MetaItemProperty
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_meta_item_property")
 */
class MetaItemProperty extends AbstractMetaItem
{

    public const META_TYPES = [
        AbstractMetaItem::META_TYPE_FIELD => 'app.meta_item.entity.meta_type_choices.field',
        AbstractMetaItem::META_TYPE_ADMIN_FIELD => 'app.meta_item.entity.meta_type_choices.custom_field',
        AbstractMetaItem::META_TYPE_TAB => 'app.meta_item.entity.meta_type_choices.tab',
        AbstractMetaItem::META_TYPE_GROUP => 'app.meta_item.entity.meta_type_choices.group',
    ];

    /**
     * Meta item
     * @var MetaItem|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\MetaData\MetaItem", inversedBy="metaItemProperties")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="placeholder", type="string", length=255, nullable=true)
     */
    protected $placeholder;

    /**
     * Use property to calculate completion ratio of entity
     *
     * @var bool|null
     *
     * @ORM\Column(name="use_for_completeness_calculation", type="boolean", nullable=true)
     */
    protected $useForCompletenessCalculation = false;

    /**
     * Is required
     *
     * @var bool
     *
     * @ORM\Column(name="required", type="boolean")
     */
    protected $required = false;

    /**
     * @return MetaItem|null
     */
    public function getParent(): ?MetaItem
    {
        return $this->parent;
    }

    /**
     * @param MetaItem|null $parent
     */
    public function setParent(?MetaItem $parent): void
    {
        $this->parent = $parent;
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
     * @return bool|null
     */
    public function getUseForCompletenessCalculation(): ?bool
    {
        return $this->useForCompletenessCalculation;
    }

    /**
     * @param bool|null $useForCompletenessCalculation
     */
    public function setUseForCompletenessCalculation(?bool $useForCompletenessCalculation): void
    {
        $this->useForCompletenessCalculation = $useForCompletenessCalculation;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Merge the properties of the current item with the given item
     * @param MetaItemProperty $mergeItem
     * @param bool $overrideIfEmpty
     */
    public function merge(MetaItemProperty $mergeItem, bool $overrideIfEmpty = false): void
    {

        if (($overrideIfEmpty || !empty($mergeItem->getInternalLabel()))
            && $mergeItem->getInternalLabel() !== $this->getInternalLabel()) {
            $this->setInternalLabel($mergeItem->getInternalLabel());
        }
        if (($overrideIfEmpty || !empty($mergeItem->getDescription()))
            && $mergeItem->getDescription() !== $this->getDescription()) {
            $this->setDescription($mergeItem->getDescription());
        }
        if (($overrideIfEmpty || !empty($mergeItem->getCustomLabel()))
            && $mergeItem->getCustomLabel() !== $this->getCustomLabel()) {
            $this->setCustomLabel($mergeItem->getCustomLabel());
        }
        if (($overrideIfEmpty || !empty($mergeItem->getPlaceholder()))
            && $mergeItem->setPlaceholder() !== $this->getPlaceholder()) {
            $this->setPlaceholder($mergeItem->getPlaceholder());
        }
    }

    /**
     * @return string|null
     */
    public function getTypeLabelKey(): ?string
    {
        $type = $this->getMetaType();
        return self::META_TYPES[$type] ?? null;
    }
}
