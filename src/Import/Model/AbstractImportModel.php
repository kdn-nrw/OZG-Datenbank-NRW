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

abstract class AbstractImportModel implements PropertyMappingInterface
{
    /**
     * Contains the raw data from the result
     * @var array
     */
    protected $rawData = [];

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

    /**
     * @param array $rawData
     */
    public function setRawData(array $rawData): void
    {
        $this->rawData = $rawData;
    }

    /**
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * Returns the import key data
     *
     * @return array|null
     */
    abstract public function getImportKeyData(): ?array;

    public function __toString()
    {
        return get_class($this);
    }
}