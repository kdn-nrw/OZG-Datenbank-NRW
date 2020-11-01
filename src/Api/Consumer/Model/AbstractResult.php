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

namespace App\Api\Consumer\Model;

abstract class AbstractResult implements ApiSearchModelInterface
{
    /**
     * Contains the data from the result that were not mapped to a result property
     * @var array
     */
    protected $unmappedData = [];

    /**
     * @return array
     */
    public function getUnmappedData(): array
    {
        return $this->unmappedData;
    }

    /**
     * @param array $unmappedData
     */
    public function setUnmappedData(array $unmappedData): void
    {
        $this->unmappedData = $unmappedData;
    }

}