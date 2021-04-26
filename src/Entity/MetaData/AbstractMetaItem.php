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

use App\Entity\Base\BaseEntity;
use App\Entity\Base\CustomEntityLabelInterface;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class AbstractMetaItem
 */
abstract class AbstractMetaItem extends BaseEntity implements CustomEntityLabelInterface
{
    public const META_TYPE_ENTITY = 'entity';
    public const META_TYPE_FIELD = 'field';
    public const META_TYPE_ADMIN_FIELD = 'admin_field';
    public const META_TYPE_TAB = 'tab';
    public const META_TYPE_GROUP = 'group';

    public const META_TYPES = [
        MetaItem::META_TYPE_ENTITY => 'app.meta_item.entity.meta_type_choices.entity',
    ];

    /**
     * The meta data type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="meta_type", length=255, nullable=false)
     */
    protected $metaType = self::META_TYPE_FIELD;

    /**
     * Meta item key
     * @var string|null
     *
     * @ORM\Column(type="string", name="meta_key", length=255, nullable=true)
     */
    protected $metaKey;

    /**
     * Meta item key
     * @var string|null
     *
     * @ORM\Column(type="string", name="internal_label", length=255, nullable=true)
     */
    protected $internalLabel;

    /**
     * Optional custom label for this meta item
     *
     * @var string|null
     *
     * @ORM\Column(type="string", name="custom_label", length=255, nullable=true)
     */
    protected $customLabel;

    /**
     * Maturity description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = '';

    /**
     * @return string
     */
    public function getMetaType(): string
    {
        return $this->metaType;
    }

    /**
     * @param string $metaType
     */
    public function setMetaType(string $metaType): void
    {
        $this->metaType = $metaType;
    }

    /**
     * @return string|null
     */
    public function getMetaKey(): ?string
    {
        return $this->metaKey;
    }

    /**
     * @param string|null $metaKey
     */
    public function setMetaKey(?string $metaKey): void
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return string|null
     */
    public function getInternalLabel(): ?string
    {
        return $this->internalLabel;
    }

    /**
     * @param string|null $internalLabel
     */
    public function setInternalLabel(?string $internalLabel): void
    {
        $this->internalLabel = $internalLabel;
    }

    /**
     * @return string|null
     */
    public function getCustomLabel(): ?string
    {
        return $this->customLabel;
    }

    /**
     * @param string|null $customLabel
     */
    public function setCustomLabel(?string $customLabel): void
    {
        $this->customLabel = $customLabel;
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
    public function getLabelKey(): ?string
    {
        return $this->getInternalLabel() ?? $this->getMetaKey();
    }

    /**
     * Returns the string representation of this item
     * @return string
     */
    public function __toString(): string
    {
        return $this->getMetaType() . ' (' . $this->getMetaKey() . ')';
    }
}
