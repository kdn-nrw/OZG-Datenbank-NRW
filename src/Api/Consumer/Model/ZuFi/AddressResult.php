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

class AddressResult extends AbstractResult
{
    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="typ", dataType="string", required=false)
     */
    protected $type;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="strasse", dataType="string", required=false)
     */
    protected $street;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="hausnummer", dataType="string", required=false)
     */
    protected $streetNumber;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="postleitzahl", dataType="string", required=false)
     */
    protected $zipCode;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="ort", dataType="string", required=false)
     */
    protected $town;

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     */
    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string|null
     */
    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    /**
     * @param string|null $streetNumber
     */
    public function setStreetNumber(?string $streetNumber): void
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     */
    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string|null
     */
    public function getTown(): ?string
    {
        return $this->town;
    }

    /**
     * @param string|null $town
     */
    public function setTown(?string $town): void
    {
        $this->town = $town;
    }


    public function __toString()
    {
        $address = trim($this->getStreet() . ' ' . $this->getStreetNumber());
        $location = trim($this->getZipCode() . ' ' . $this->getTown());
        return trim($address . "\n" . $location);
    }
}
