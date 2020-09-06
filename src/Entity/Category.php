<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Category
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_category")
 */
class Category extends BaseNamedEntity implements CategoryEntityInterface, ImportEntityInterface
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
     * @var ArrayCollection|Category[]
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    protected $children;

    /**
     * Many Categories have One Category.
     *
     * @var Category|null
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var Contact[]|Collection
     * @ORM\ManyToMany(targetEntity="Contact", mappedBy="categories")
     */
    protected $contacts;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->contacts = new ArrayCollection();
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
    public function addChild($category): self
    {
        if (!$this->children->contains($category) && $category->getId() !== $this->getId()) {
            $this->children->add($category);
            $category->setParent($this);
        }

        return $this;
    }

    /**
     * @param Category $category
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
    public function getParent(): ?CategoryEntityInterface
    {
        return $this->parent;
    }

    /**
     * @param Category|null $parent
     */
    public function setParent(?Category $parent): void
    {
        if (null === $parent || $parent->getId() !== $this->getId()) {
            $this->parent = $parent;
        }
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function addContact($contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->addCategory($this);
        }

        return $this;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function removeContact($contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            $contact->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param Contact[]|Collection $contacts
     */
    public function setContacts($contacts): void
    {
        $this->contacts = $contacts;
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
