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
        MetaItemProperty::META_TYPE_FIELD => 'app.meta_item.entity.meta_type_choices.field',
        MetaItemProperty::META_TYPE_ADMIN_FIELD => 'app.meta_item.entity.meta_type_choices.custom_field',
        MetaItem::META_TYPE_TAB => 'app.meta_item.entity.meta_type_choices.tab',
        MetaItem::META_TYPE_GROUP => 'app.meta_item.entity.meta_type_choices.group',
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
