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

use App\Entity\Organisation;
use App\Import\Annotation\ImportModelAnnotation;

/**
 * Class ModelRegionProjectImportModel
 * @ImportModelAnnotation(targetEntity="\App\Entity\ModelRegion\ModelRegionProject")
 */
class ModelRegionProjectImportModel extends AbstractImportModel
{
    /**
     * @var int|null
     *
     * @ImportModelAnnotation(parameter="id", dataType="int", required=true, autoIncrement=true)
     */
    protected $importId;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="projekttitel", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="projektbeschreibung", dataType="string", required=false)
     */
    protected $description;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="beginn_durchfuehrungszeitraum", dataType="date", required=false)
     */
    protected $projectStartAt;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="ende_durchfuehrungszeitraum", dataType="date", required=false)
     */
    protected $projectEndAt;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="alleinstellungsmerkmal_innovation_", dataType="string", required=false)
     */
    protected $usp;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="nutzen_fuer_alle_nrw_kommunen_", dataType="string", required=false)
     */
    protected $communesBenefits;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="uebertragbare_bzw_lizenzfreie_loesung", dataType="string", required=false)
     */
    protected $transferableService;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="geplanter_zeitpunkt_der_verfuegbarkeit_uebertragung_jahr_monat", dataType="string", required=false)
     */
    protected $transferableStart;

    /**
     * @var Organisation[]|null
     *
     * @ImportModelAnnotation(parameter="zuwendungsempfaenger", dataType="callback", required=false)
     */
    protected $organisations;

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
     * @return int|null
     */
    public function getImportId(): ?int
    {
        return $this->importId;
    }

    /**
     * @param int|null $importId
     */
    public function setImportId(?int $importId): void
    {
        $this->importId = $importId;
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
    public function getProjectStartAt(): ?string
    {
        return $this->projectStartAt;
    }

    /**
     * @param string|null $projectStartAt
     */
    public function setProjectStartAt(?string $projectStartAt): void
    {
        $this->projectStartAt = $projectStartAt;
    }

    /**
     * @return string|null
     */
    public function getProjectEndAt(): ?string
    {
        return $this->projectEndAt;
    }

    /**
     * @param string|null $projectEndAt
     */
    public function setProjectEndAt(?string $projectEndAt): void
    {
        $this->projectEndAt = $projectEndAt;
    }

    /**
     * @return string|null
     */
    public function getUsp(): ?string
    {
        return $this->usp;
    }

    /**
     * @param string|null $usp
     */
    public function setUsp(?string $usp): void
    {
        $this->usp = $usp;
    }

    /**
     * @return string|null
     */
    public function getCommunesBenefits(): ?string
    {
        return $this->communesBenefits;
    }

    /**
     * @param string|null $communesBenefits
     */
    public function setCommunesBenefits(?string $communesBenefits): void
    {
        $this->communesBenefits = $communesBenefits;
    }

    /**
     * @return string|null
     */
    public function getTransferableService(): ?string
    {
        return $this->transferableService;
    }

    /**
     * @param string|null $transferableService
     */
    public function setTransferableService(?string $transferableService): void
    {
        $this->transferableService = $transferableService;
    }

    /**
     * @return string|null
     */
    public function getTransferableStart(): ?string
    {
        return $this->transferableStart;
    }

    /**
     * @param string|null $transferableStart
     */
    public function setTransferableStart(?string $transferableStart): void
    {
        $this->transferableStart = $transferableStart;
    }

    /**
     * @return Organisation[]|null
     */
    public function getOrganisations(): ?array
    {
        return $this->organisations;
    }

    /**
     * @param Organisation[]|null $organisations
     */
    public function setOrganisations(?array $organisations): void
    {
        $this->organisations = $organisations;
    }

    /**
     * Returns the import key data
     *
     * @return array|null
     */
    public function getImportKeyData(): ?array
    {
        return ['importId' => $this->getImportId()];
    }


    public function __toString()
    {
        return (string)$this->getName();
    }
}
