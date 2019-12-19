<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * Category trait
 * @property ArrayCollection $categories
 */
trait CategoryTrait
{
    /**
     * @param Category $category
     * @return self
     */
    public function addCategory($category)
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * @param Category $category
     * @return self
     */
    public function removeCategory($category)
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Category[]|Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param Category[]|Collection $categories
     */
    public function setCategories($categories): void
    {
        $this->categories = $categories;
    }
}
