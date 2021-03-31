<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Base;

/**
 * SortableEntityInterface
 */
interface SortableEntityInterface
{
    /**
     * @return int
     */
    public function getPosition(): int;

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void;
}
