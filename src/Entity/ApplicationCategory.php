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
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Category
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_application_category")
 * @ApiResource
 */
class ApplicationCategory extends BaseNamedEntity implements CategoryEntityInterface, ImportEntityInterface
{
    use ImportTrait;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * One Category has Many Categories.
     *
     * @var ArrayCollection|ApplicationCategory[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ApplicationCategory", mappedBy="parent")
     */
    protected $children;

    /**
     * Many Categories have One Category.
     *
     * @var ApplicationCategory|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ApplicationCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $parent;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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
     * @param ApplicationCategory $category
     * @return self
     */
    public function addChild($category): self
    {
        if (!$this->children->contains($category) && $category->getId() !== $this->getId()) {
            $this->children->add($category);
            $category->setParent($this);
        }

        return $this;
    }

    /**
     * @param ApplicationCategory $category
     * @return self
     */
    public function removeChild($category): self
    {
        if ($this->children->contains($category)) {
            $this->children->removeElement($category);
            $category->setParent($this->getParent());
        }

        return $this;
    }

    /**
     * @return ApplicationCategory[]|ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ApplicationCategory[]|ArrayCollection $children
     */
    public function setChildren($children): void
    {
        $this->children = $children;
    }

    /**
     * @return ApplicationCategory|null
     */
    public function getParent(): ?CategoryEntityInterface
    {
        return $this->parent;
    }

    /**
     * @param ApplicationCategory|null $parent
     */
    public function setParent(?ApplicationCategory $parent): void
    {
        if (null === $parent || $parent->getId() !== $this->getId()) {
            $this->parent = $parent;
        }
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function __toString(): string
    {
        return $this->getName() ?: 'n/a';
    }

}
