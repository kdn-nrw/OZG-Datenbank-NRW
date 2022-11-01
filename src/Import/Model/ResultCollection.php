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

use Doctrine\Common\Collections\ArrayCollection;

class ResultCollection extends ArrayCollection implements PropertyMappingInterface
{
    /**
     * @var int
     */
    protected $totalResultCount = 0;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $resultsPerPage = 10;
    /**
     * Contains the data from the result that were not mapped to a result property
     * @var array
     */
    protected $unmappedData = [];
    /**
     * Contains the raw data from the result
     * @var array
     */
    protected $rawData = [];

    /**
     * @var array|string
     */
    protected $mapProperties = [];

    /**
     * @return int
     */
    public function getTotalResultCount(): int
    {
        return $this->totalResultCount;
    }

    /**
     * @param int $totalResultCount
     */
    public function setTotalResultCount(int $totalResultCount): void
    {
        $this->totalResultCount = $totalResultCount;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getResultsPerPage(): int
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     */
    public function setResultsPerPage(int $resultsPerPage): void
    {
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * Returns true if the results are paginated
     * @return bool
     */
    public function isPaginationEnabled(): bool
    {
        return $this->totalResultCount > $this->resultsPerPage;
    }

    /**
     * Returns true if the pagination is on the last page
     *
     * @return bool
     */
    public function isLastPage(): bool
    {
        return ($this->page * $this->resultsPerPage) + $this->resultsPerPage >= $this->totalResultCount;
    }

    /**
     * Returns the last page number
     *
     * @return int
     */
    public function getLastPageNumber(): int
    {
        return (int) ceil($this->totalResultCount / $this->resultsPerPage);
    }

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
     * Add property field mapping
     *
     * @param string $property
     * @param string $mappedField
     */
    public function addPropertyMapping(string $property, string $mappedField):void
    {
        $this->mapProperties[$property] = $mappedField;
    }

    /**
     * Get remote field name for property
     *
     * @param string $property
     * @return string
     */
    public function getPropertyMapping(string $property): string
    {
        return $this->mapProperties[$property] ?? $property;
    }

    /**
     * Returns true, if the result collection is valid, i.e. not empty
     */
    public function isValid(): bool
    {
        return !$this->isEmpty();
    }
}
