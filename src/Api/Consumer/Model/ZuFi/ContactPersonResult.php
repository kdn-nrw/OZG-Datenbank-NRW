<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api\Consumer\Model\ZuFi;

use App\Api\Annotation\ApiSearchModelAnnotation;
use App\Api\Consumer\Model\AbstractResult;
use App\Import\Model\ResultCollection;

class ContactPersonResult extends AbstractResult
{
    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="anrede", dataType="string", required=false)
     */
    protected $salutation;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="titel", dataType="string", required=false)
     */
    protected $title;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="vorname", dataType="string", required=false)
     */
    protected $firstName;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="familienname", dataType="string", required=true)
     */
    protected $lastName;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="position", dataType="string", required=false)
     */
    protected $position;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="sprechzeiten", dataType="string", required=false)
     */
    protected $officeHours;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="raum", dataType="string", required=false)
     */
    protected $room;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="anschrift", dataType="string", required=false)
     */
    protected $address;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="internetadresse", dataType="string", required=false)
     */
    protected $url;

    /**
     * @var ResultCollection|CommunicationResult[]
     * @ApiSearchModelAnnotation(parameter="kommunikation", modelClass="App\Api\Consumer\Model\ZuFi\CommunicationResult", dataType="collection", required=false)
     */
    protected $communications;

    public function __construct()
    {
        $this->communications = new ResultCollection();
    }

    /**
     * @return string|null
     */
    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    /**
     * @param string|null $salutation
     */
    public function setSalutation(?string $salutation): void
    {
        $this->salutation = $salutation;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param string|null $position
     */
    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return string|null
     */
    public function getOfficeHours(): ?string
    {
        return $this->officeHours;
    }

    /**
     * @param string|null $officeHours
     */
    public function setOfficeHours(?string $officeHours): void
    {
        $this->officeHours = $officeHours;
    }

    /**
     * @return string|null
     */
    public function getRoom(): ?string
    {
        return $this->room;
    }

    /**
     * @param string|null $room
     */
    public function setRoom(?string $room): void
    {
        $this->room = $room;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
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

    public function getName(): string
    {
        $name = trim($this->getFirstName() . ' ' . $this->getLastName());
        if ($name) {
            $title = $this->getTitle();
            if ($title) {
                $name = $title . ' ' . $name;
            }
        }
        return $name;
    }

    public function __toString()
    {
        return trim($this->getSalutation() . ' ' . $this->getName());
    }
}
