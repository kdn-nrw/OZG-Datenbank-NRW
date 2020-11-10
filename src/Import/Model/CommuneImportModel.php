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

use App\Entity\StateGroup\AdministrativeDistrict;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\CommuneType;
use App\Import\Annotation\ImportModelAnnotation;

/**
 * Class CommuneImportModel
 * @ImportModelAnnotation(targetEntity="\App\Entity\StateGroup\Commune")
 */
class CommuneImportModel extends AbstractImportModel
{
    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="Kommune/Kreis", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var CommuneType|null
     *
     * @ImportModelAnnotation(parameter="Kategorie", dataType="model", required=false, targetEntity="\App\Entity\StateGroup\CommuneType", mapToProperty="name")
     */
    protected $communeType;

    /**
     * @var Commune|null
     *
     * @ImportModelAnnotation(parameter="Zugehörigkeit Kreis", dataType="callback", required=false)
     */
    protected $constituency;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="PLZ", dataType="string", required=false)
     */
    protected $zipCode;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="Ort", dataType="string", required=false)
     */
    protected $town;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="Strasse", dataType="string", required=false)
     */
    protected $street;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="Internet", dataType="string", required=false)
     */
    protected $url;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="E-Mail", dataType="string", required=false)
     */
    protected $mainEmail;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="AGS", dataType="string", required=false)
     */
    protected $officialCommunityKey;

    /**
     * @var AdministrativeDistrict|null
     *
     * @ImportModelAnnotation(parameter="Reg-Bez", dataType="model", required=false, targetEntity="\App\Entity\StateGroup\AdministrativeDistrict", mapToProperty="name")
     */
    protected $administrativeDistrict;

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
     * @return CommuneType|null
     */
    public function getCommuneType(): ?CommuneType
    {
        return $this->communeType;
    }

    /**
     * @param CommuneType|null $communeType
     */
    public function setCommuneType(?CommuneType $communeType): void
    {
        $this->communeType = $communeType;
    }

    /**
     * @return Commune|null
     */
    public function getConstituency(): ?Commune
    {
        return $this->constituency;
    }

    /**
     * @param Commune|null $constituency
     */
    public function setConstituency(?Commune $constituency): void
    {
        $this->constituency = $constituency;
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
     * @return string|null
     */
    public function getMainEmail(): ?string
    {
        return $this->mainEmail;
    }

    /**
     * @param string|null $mainEmail
     */
    public function setMainEmail(?string $mainEmail): void
    {
        $this->mainEmail = $mainEmail;
    }

    /**
     * @return string|null
     */
    public function getOfficialCommunityKey(): ?string
    {
        return $this->officialCommunityKey;
    }

    /**
     * @param string|null $officialCommunityKey
     */
    public function setOfficialCommunityKey(?string $officialCommunityKey): void
    {
        $this->officialCommunityKey = $officialCommunityKey;
    }

    /**
     * @return AdministrativeDistrict|null
     */
    public function getAdministrativeDistrict(): ?AdministrativeDistrict
    {
        return $this->administrativeDistrict;
    }

    /**
     * @param AdministrativeDistrict|null $administrativeDistrict
     */
    public function setAdministrativeDistrict(?AdministrativeDistrict $administrativeDistrict): void
    {
        $this->administrativeDistrict = $administrativeDistrict;
    }

    /**
     * Returns the import key data
     *
     * @return array|null
     */
    public function getImportKeyData(): ?array
    {
        $communeType = $this->getCommuneType();
        $communeTypeId = null !== $communeType ? $communeType->getId() : null;
        return [
            'name' => $this->getName(),
            'communeType' => $communeTypeId,
        ];
    }

    public function __toString()
    {
        return $this->getName() . ($this->getZipCode() ? ' ['.$this->getZipCode().']' : '');
    }
}
