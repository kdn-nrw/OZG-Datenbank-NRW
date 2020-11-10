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
use App\Api\Consumer\Model\AbstractResult;
use App\Import\Model\ResultCollection;

class OrganisationResult extends AbstractResult
{
    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="name", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="identifikator", dataType="string", required=true)
     */
    protected $key;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="beschreibung", dataType="string", required=false)
     */
    protected $description;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="mandant", dataType="string", required=true)
     */
    protected $mandator;

    /**
     * @var ResultCollection|AddressResult[]
     * @ApiSearchModelAnnotation(parameter="anschrift", modelClass="App\Api\Consumer\Model\ZuFi\AddressResult", dataType="collection", required=false)
     */
    protected $addresses;

    /**
     * @var ResultCollection|CommunicationResult[]
     * @ApiSearchModelAnnotation(parameter="kommunikation", modelClass="App\Api\Consumer\Model\ZuFi\CommunicationResult", dataType="collection", required=false)
     */
    protected $communications;

    public function __construct()
    {
        $this->addresses = new ResultCollection();
        $this->communications = new ResultCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param string|null $key
     */
    public function setKey(?string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getMandator(): ?string
    {
        return $this->mandator;
    }

    /**
     * @param string|null $mandator
     */
    public function setMandator(?string $mandator): void
    {
        $this->mandator = $mandator;
    }

    /**
     * @return ResultCollection|AddressResult[]
     */
    public function getAddresses(): ResultCollection
    {
        return $this->addresses;
    }

    /**
     * @param ResultCollection|AddressResult[] $addresses
     */
    public function setAddresses(ResultCollection $addresses): void
    {
        $this->addresses = $addresses;
    }

    /**
     * @param AddressResult $address
     */
    public function addAddress(AddressResult $address): void
    {
        $this->addresses->add($address);
    }

    /**
     * @return ResultCollection|CommunicationResult[]
     */
    public function getCommunications(): ResultCollection
    {
        return $this->communications;
    }

    /**
     * @param ResultCollection|CommunicationResult[] $communications
     */
    public function setCommunications(ResultCollection $communications): void
    {
        $this->communications = $communications;
    }

    /**
     * @param CommunicationResult $communication
     */
    public function addCommunication(CommunicationResult $communication): void
    {
        $this->communications->add($communication);
    }

    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getKey();
    }
}
