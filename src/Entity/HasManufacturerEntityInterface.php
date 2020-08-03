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

namespace App\Entity;


use Doctrine\Common\Collections\Collection;

/**
 * Interface for entities with manufacturers property
 */
interface HasManufacturerEntityInterface
{

    /**
     * Returns the unique manufacturer list with the manufacturer determined through the specialized procedures
     *
     * @return Manufacturer[]|Collection
     */
    public function getManufacturers(): Collection;
}
