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

class ArsAgsResult extends AbstractResult
{
    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="name", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="regionalschluessel", dataType="string", required=false)
     */
    protected $regionalKey;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="gemeindeschluessel", dataType="string", required=false)
     */
    protected $communeKey;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="verbandsschluessel", dataType="string", required=false)
     */
    protected $associationKey;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="landesregierung", dataType="string", required=false)
     */
    protected $stateGovernment;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="collectionschluessel", dataType="string", required=false)
     */
    protected $collectionKey;

    /**
     * @var string[]|array
     * @ApiSearchModelAnnotation(parameter="postleitzahlen", dataType="array", required=false)
     */
    protected $zipCodes = [];

    /**
     * @var bool|null
     * @ApiSearchModelAnnotation(parameter="durchsuchbar", dataType="bool", required=false)
     */
    protected $searchable;

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
    public function getRegionalKey(): ?string
    {
        return $this->regionalKey;
    }

    /**
     * @param string|null $regionalKey
     */
    public function setRegionalKey(?string $regionalKey): void
    {
        $this->regionalKey = $regionalKey;
    }

    /**
     * @return string|null
     */
    public function getCommuneKey(): ?string
    {
        return $this->communeKey;
    }

    /**
     * @param string|null $communeKey
     */
    public function setCommuneKey(?string $communeKey): void
    {
        $this->communeKey = $communeKey;
    }

    /**
     * @return string|null
     */
    public function getAssociationKey(): ?string
    {
        return $this->associationKey;
    }

    /**
     * @param string|null $associationKey
     */
    public function setAssociationKey(?string $associationKey): void
    {
        $this->associationKey = $associationKey;
    }

    /**
     * @return string|null
     */
    public function getStateGovernment(): ?string
    {
        return $this->stateGovernment;
    }

    /**
     * @param string|null $stateGovernment
     */
    public function setStateGovernment(?string $stateGovernment): void
    {
        $this->stateGovernment = $stateGovernment;
    }

    /**
     * @return string|null
     */
    public function getCollectionKey(): ?string
    {
        return $this->collectionKey;
    }

    /**
     * @param string|null $collectionKey
     */
    public function setCollectionKey(?string $collectionKey): void
    {
        $this->collectionKey = $collectionKey;
    }

    /**
     * @return array|string[]
     */
    public function getZipCodes(): array
    {
        return $this->zipCodes;
    }

    /**
     * @param array|string[] $zipCodes
     */
    public function setZipCodes(array $zipCodes): void
    {
        $this->zipCodes = $zipCodes;
    }

    /**
     * @return bool|null
     */
    public function getSearchable(): ?bool
    {
        return $this->searchable;
    }

    /**
     * @param bool|null $searchable
     */
    public function setSearchable(?bool $searchable): void
    {
        $this->searchable = $searchable;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}
