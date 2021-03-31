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

namespace App\Entity\Configuration;

use Doctrine\Common\Collections\Collection;

/**
 * An interface for all entities
 */
interface HasCustomFieldsEntityInterface
{

    /**
     * The implementing function must set the reference to the current entity!
     *
     * @param CustomValue $customValue
     * @return void
     */
    public function addCustomValue(CustomValue $customValue): void;

    /**
     * @return Collection|CustomValue[]
     */
    public function getCustomValues(): Collection;
}
