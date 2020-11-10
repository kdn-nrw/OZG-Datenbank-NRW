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

use App\Api\Annotation\ApiSearchModelAnnotation;

abstract class AbstractDemand
{
    protected const REQUEST_METHOD_GET = 'GET';
    public const RESULTS_PER_PAGE = 10;

    /**
     * @var int|null
     */
    protected $page;

    /**
     * @var int|null
     * @ApiSearchModelAnnotation(parameter="start", dataType="int", required=false, customProperty=true, searchProperty=false)
     */
    protected $offset;

    /**
     * @var int|null
     * @ApiSearchModelAnnotation(parameter="anzahl", dataType="int", required=false, customProperty=true, searchProperty=false)
     */
    protected $resultsPerPage;

    public function getRequestMethod(): string
    {
        return self::REQUEST_METHOD_GET;
    }

    /**
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        if ($page > 1) {
            if (!$this->resultsPerPage) {
                $this->setResultsPerPage(self::RESULTS_PER_PAGE);
            }
            $this->setOffset(($page - 1) * (int) $this->getResultsPerPage());
        }
        $this->page = $page;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     */
    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return int|null
     */
    public function getResultsPerPage(): ?int
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int|null $resultsPerPage
     */
    public function setResultsPerPage(?int $resultsPerPage): void
    {
        $this->resultsPerPage = $resultsPerPage;
    }

}