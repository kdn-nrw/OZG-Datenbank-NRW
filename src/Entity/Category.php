<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Category
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_category")
 */
class Category extends BaseNamedEntity
{

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * One Category has Many Categories.
     *
     * @var ArrayCollection|Category[]
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

    /**
     * Many Categories have One Category.
     *
     * @var Category|null
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

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
     * @param Category $category
     * @return self
     */
    public function addChild($category)
    {
        if ($category->getId() !== $this->getId() && !$this->children->contains($category)) {
            $this->children->add($category);
            $category->setParent($this);
        }

        return $this;
    }

    /**
     * @param Category $category
     * @return self
     */
    public function removeChild($category)
    {
        if ($this->children->contains($category)) {
            $this->children->removeElement($category);
            $category->setParent($this->getParent());
        }

        return $this;
    }

    /**
     * @return Category[]|ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Category[]|ArrayCollection $children
     */
    public function setChildren($children): void
    {
        $this->children = $children;
    }

    /**
     * @return Category|null
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @param Category|null $parent
     */
    public function setParent(?Category $parent): void
    {
        if ($parent->getId() !== $this->getId()) {
            $this->parent = $parent;
        }
    }

    public function __toString(): string
    {
        return $this->getName() ?: 'n/a';
    }

}
