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
 * HasImageEntityInterface interface
 */
interface HideableEntityInterface
{
    /**
     * @return bool
     */
    public function getHidden(): bool;

    /**
     * @return bool
     */
    public function isHidden(): bool;

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden);
}
