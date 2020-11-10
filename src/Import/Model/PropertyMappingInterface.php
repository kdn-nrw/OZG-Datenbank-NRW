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

namespace App\Import\Model;

interface PropertyMappingInterface
{
    /**
     * @return array
     */
    public function getUnmappedData(): array;

    /**
     * @param array $unmappedData
     */
    public function setUnmappedData(array $unmappedData): void;

}