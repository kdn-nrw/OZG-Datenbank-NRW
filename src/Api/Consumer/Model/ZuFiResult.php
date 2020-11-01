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
use App\Api\Consumer\Model\ZuFi\OrganisationResult;
use App\Api\Consumer\Model\ZuFi\ServiceResult;

class ZuFiResult extends AbstractResult
{
    /**
     * @var ServiceResult|null
     * @ApiSearchModelAnnotation(parameter="leistung", dataType="model", modelClass="App\Api\Consumer\Model\ZuFi\ServiceResult", required=true)
     */
    protected $service;

    /**
     * @var ResultCollection|OrganisationResult[]
     * @ApiSearchModelAnnotation(parameter="organisationseinheit", modelClass="App\Api\Consumer\Model\ZuFi\OrganisationResult", dataType="modelCollection", required=true)
     */
    protected $organisations;

    public function __construct()
    {
        $this->organisations = new ResultCollection();
    }

    /**
     * @return ServiceResult|null
     */
    public function getService(): ?ServiceResult
    {
        return $this->service;
    }

    /**
     * @param ServiceResult|null $service
     */
    public function setService(?ServiceResult $service): void
    {
        $this->service = $service;
    }

    /**
     * @return ResultCollection|OrganisationResult[]
     */
    public function getOrganisations(): ResultCollection
    {
        return $this->organisations;
    }

    /**
     * @param ResultCollection|OrganisationResult[] $organisations
     */
    public function setOrganisations(ResultCollection $organisations): void
    {
        $this->organisations = $organisations;
    }

    /**
     * @param OrganisationResult $organisation
     */
    public function addOrganisation(OrganisationResult $organisation): void
    {
        $this->organisations->add($organisation);
    }

    public function __toString()
    {
        if ($this->service) {
            return trim($this->service . ' - ' . $this->service->getAuthor());
        }
        if ($this->organisations->count() > 0) {
            $organisation = $this->organisations->first();
            /** @var OrganisationResult $organisation */
            return $organisation . '';
        }
        return '';
    }

}
