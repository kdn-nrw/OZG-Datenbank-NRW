<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Base;

use DateTime;

/**
 * Interface AnonymizableEntityInterface
 * @see AnonymizableEntityTrait (provides field "anonymizedAt")
 */
interface AnonymizableEntityInterface
{
    /**
     * @return DateTime|null
     */
    public function getAnonymizedAt(): ?DateTime;

    /**
     * @param DateTime|null $anonymizedAt
     */
    public function setAnonymizedAt(?DateTime $anonymizedAt);
}
