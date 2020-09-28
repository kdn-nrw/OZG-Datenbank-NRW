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

namespace App\Entity\Base;

/**
 * SluggableInterface Interface
 */
interface SluggableInterface
{
    /**
     * Get slug
     *
     * @return string|null
     */
    public function getSlug(): ?string;

    /**
     * Sets the slug (used in case Gedmo Sluggable is not working)
     *
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void;

}
