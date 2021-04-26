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

use App\Util\SnakeCaseConverter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class MetaItem
 *
 * @ORM\Entity(repositoryClass="App\Entity\Repository\MetaItemRepository")
 * @ORM\Table(name="ozg_meta_item")
 */
class MetaItem extends AbstractMetaItem
{

    /**
     * The meta data type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="meta_type", length=255, nullable=false)
     */
    protected $metaType = self::META_TYPE_ENTITY;

    /**
     * @var MetaItemProperty[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\MetaData\MetaItemProperty", mappedBy="parent", cascade={"all"})
     */
    protected $metaItemProperties;

    /**
     * MetaItem constructor.
     */
    public function __construct()
    {
        $this->metaItemProperties = new ArrayCollection();
    }


    /**
     * @param MetaItemProperty $metaItemProperty
     * @return self
     */
    public function addMetaItemProperty($metaItemProperty): self
    {
        if (null !== $existingProperty = $this->getMetaItemProperty($metaItemProperty->getMetaKey())) {
            $existingProperty->merge($metaItemProperty);
            $existingProperty->setParent($this);
        } elseif (!$this->metaItemProperties->contains($metaItemProperty)) {
            $this->metaItemProperties->add($metaItemProperty);
            $metaItemProperty->setParent($this);
        }

        return $this;
    }

    /**
     * @param MetaItemProperty $metaItemProperty
     * @return self
     */
    public function removeMetaItemProperty($metaItemProperty): self
    {
        if (null !== $existingProperty = $this->getMetaItemProperty($metaItemProperty->getMetaKey())) {
            $this->metaItemProperties->removeElement($existingProperty);
        }

        return $this;
    }

    /**
     * @return MetaItemProperty[]|Collection
     */
    public function getMetaItemProperties()
    {
        return $this->metaItemProperties;
    }

    /**
     * @param MetaItemProperty[]|Collection $metaItemProperties
     */
    public function setMetaItemProperties($metaItemProperties): void
    {
        $this->metaItemProperties = $metaItemProperties;
    }

    /**
     * Returns the meta item property with the given key or null if the property does not exist
     *
     * @param string $key
     * @return MetaItemProperty|null
     */
    public function getMetaItemProperty(string $key): ?MetaItemProperty
    {
        $metaKey = SnakeCaseConverter::camelCaseToSnakeCase($key);
        foreach ($this->getMetaItemProperties() as $metaItemProperty) {
            /** @var MetaItemProperty $metaItemProperty */
            if ($metaItemProperty->getMetaKey() === $metaKey) {
                return $metaItemProperty;
            }
        }
        return null;
    }
}
