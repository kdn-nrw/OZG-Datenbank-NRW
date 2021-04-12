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
 * HasGroupEmailEntityInterface interface:
 * Used by email services to send emails to a common email address (assigned to any entity)
 */
interface HasGroupEmailEntityInterface
{

    /**
     * @return string|null
     */
    public function getGroupEmail(): ?string;
}
