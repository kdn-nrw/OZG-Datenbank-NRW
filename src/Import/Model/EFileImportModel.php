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

use App\Entity\EFileStatus;
use App\Entity\SpecializedProcedure;
use App\Entity\StateGroup\ServiceProvider;
use App\Import\Annotation\ImportModelAnnotation;

/**
 * Class EFileImportModel
 * @ImportModelAnnotation(targetEntity="\App\Entity\EFile")
 */
class EFileImportModel extends AbstractImportModel
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
     * @ImportModelAnnotation(parameter="DMS-Anwendung", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="Kurzbeschreibung", dataType="string", required=false)
     */
    protected $description;

    /**
     * @var ServiceProvider|null
     *
     * @ImportModelAnnotation(parameter="Betreiber", dataType="model", required=false, targetEntity="\App\Entity\StateGroup\ServiceProvider", mapToProperty="name")
     */
    protected $serviceProvider;

    /**
     * @var EFileStatus|null
     *
     * @ImportModelAnnotation(parameter="Status eAkte", dataType="model", required=false, targetEntity="\App\Entity\EFileStatus", mapToProperty="name")
     */
    protected $status;

    /**
     * @var SpecializedProcedure[]|null
     *
     * @ImportModelAnnotation(parameter="Softwarebasis", dataType="collection", required=false, targetEntity="\App\Entity\SpecializedProcedure", mapToProperty="name")
     */
    protected $specializedProcedures;

    /**
     * @var SpecializedProcedure|null
     *
     * @ImportModelAnnotation(parameter="Speichertechnik", dataType="collection", required=false, targetEntity="\App\Entity\EFileStorageType", mapToProperty="name")
     */
    protected $storageTypes;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="Informationen", dataType="string", required=false)
     */
    protected $notes;

    /**
     * @var bool|null
     *
     * @ImportModelAnnotation(parameter="Wirtschaftlichkeitsbetrachtung", dataType="boolean", required=false)
     */
    protected $hasEconomicViabilityAssessment;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="Investitionssumme", dataType="decimal", required=false)
     */
    protected $sumInvestments;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="Folgekosten", dataType="decimal", required=false)
     */
    protected $followUpCosts;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="Einsparpotentiale", dataType="string", required=false)
     */
    protected $savingPotentialNotes;

    /**
     * @var SpecializedProcedure|null
     *
     * @ImportModelAnnotation(parameter="Führendes System", dataType="model", required=false, targetEntity="\App\Entity\SpecializedProcedure", mapToProperty="name")
     */
    protected $leadingSystem;

    /**
     * @var SpecializedProcedure[]|null
     *
     * @ImportModelAnnotation(parameter="Beteiligte Softwaremodule", dataType="collection", required=false, targetEntity="\App\Entity\SpecializedProcedure", mapToProperty="name")
     */
    protected $softwareModules;

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
     * @return ServiceProvider|null
     */
    public function getServiceProvider(): ?ServiceProvider
    {
        return $this->serviceProvider;
    }

    /**
     * @param ServiceProvider|null $serviceProvider
     */
    public function setServiceProvider(?ServiceProvider $serviceProvider): void
    {
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * @return EFileStatus|null
     */
    public function getStatus(): ?EFileStatus
    {
        return $this->status;
    }

    /**
     * @param EFileStatus|null $status
     */
    public function setStatus(?EFileStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return SpecializedProcedure[]|null
     */
    public function getSpecializedProcedures(): ?array
    {
        return $this->specializedProcedures;
    }

    /**
     * @param SpecializedProcedure[]|null $specializedProcedures
     */
    public function setSpecializedProcedures(?array $specializedProcedures): void
    {
        $this->specializedProcedures = $specializedProcedures;
    }

    /**
     * @return SpecializedProcedure|null
     */
    public function getStorageTypes(): ?SpecializedProcedure
    {
        return $this->storageTypes;
    }

    /**
     * @param SpecializedProcedure|null $storageTypes
     */
    public function setStorageTypes(?SpecializedProcedure $storageTypes): void
    {
        $this->storageTypes = $storageTypes;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @return bool|null
     */
    public function getHasEconomicViabilityAssessment(): ?bool
    {
        return $this->hasEconomicViabilityAssessment;
    }

    /**
     * @param bool|null $hasEconomicViabilityAssessment
     */
    public function setHasEconomicViabilityAssessment(?bool $hasEconomicViabilityAssessment): void
    {
        $this->hasEconomicViabilityAssessment = $hasEconomicViabilityAssessment;
    }

    /**
     * @return string|null
     */
    public function getSumInvestments(): ?string
    {
        return $this->sumInvestments;
    }

    /**
     * @param string|null $sumInvestments
     */
    public function setSumInvestments(?string $sumInvestments): void
    {
        $this->sumInvestments = $sumInvestments;
    }

    /**
     * @return string|null
     */
    public function getFollowUpCosts(): ?string
    {
        return $this->followUpCosts;
    }

    /**
     * @param string|null $followUpCosts
     */
    public function setFollowUpCosts(?string $followUpCosts): void
    {
        $this->followUpCosts = $followUpCosts;
    }

    /**
     * @return string|null
     */
    public function getSavingPotentialNotes(): ?string
    {
        return $this->savingPotentialNotes;
    }

    /**
     * @param string|null $savingPotentialNotes
     */
    public function setSavingPotentialNotes(?string $savingPotentialNotes): void
    {
        $this->savingPotentialNotes = $savingPotentialNotes;
    }

    /**
     * @return SpecializedProcedure|null
     */
    public function getLeadingSystem(): ?SpecializedProcedure
    {
        return $this->leadingSystem;
    }

    /**
     * @param SpecializedProcedure|null $leadingSystem
     */
    public function setLeadingSystem(?SpecializedProcedure $leadingSystem): void
    {
        $this->leadingSystem = $leadingSystem;
    }

    /**
     * @return SpecializedProcedure[]|null
     */
    public function getSoftwareModules(): ?array
    {
        return $this->softwareModules;
    }

    /**
     * @param SpecializedProcedure[]|null $softwareModules
     */
    public function setSoftwareModules(?array $softwareModules): void
    {
        $this->softwareModules = $softwareModules;
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
