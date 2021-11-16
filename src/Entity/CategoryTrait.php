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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * Category trait
 * @property ArrayCollection $categories
 */
trait CategoryTrait
{
    /**
     * @param CategoryEntityInterface $category
     * @return self
     */
    public function addCategory(CategoryEntityInterface $category)
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * @param CategoryEntityInterface $category
     * @return self
     */
    public function removeCategory(CategoryEntityInterface $category)
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return CategoryEntityInterface[]|Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param CategoryEntityInterface[]|Collection $categories
     */
    public function setCategories($categories): void
    {
        $this->categories = $categories;
    }
}
