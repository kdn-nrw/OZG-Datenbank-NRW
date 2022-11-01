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

namespace App\Api\Consumer\Model\ZuFi;

use App\Api\Annotation\ApiSearchModelAnnotation;
use App\Entity\Service;
use App\Import\Model\ResultCollection;

class ZuFiResultCollection extends ResultCollection
{
    /**
     * @var ServiceBaseResult|null
     * @ApiSearchModelAnnotation(parameter="leistungStammtext", dataType="model", modelClass="App\Api\Consumer\Model\ZuFi\ServiceBaseResult", required=false)
     */
    protected $serviceBase;

    /**
     * @var Service|null
     */
    protected $ozgService;

    /**
     * @return ServiceBaseResult|null
     */
    public function getServiceBase(): ?ServiceBaseResult
    {
        return $this->serviceBase;
    }

    /**
     * @param ServiceBaseResult|null $serviceBase
     */
    public function setServiceBase(?ServiceBaseResult $serviceBase): void
    {
        $this->serviceBase = $serviceBase;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return parent::isValid() || null !== $this->serviceBase;
    }

    /**
     * @return Service|null
     */
    public function getOzgService(): ?Service
    {
        return $this->ozgService;
    }

    /**
     * @param Service|null $ozgService
     */
    public function setOzgService(?Service $ozgService): void
    {
        $this->ozgService = $ozgService;
    }

}
