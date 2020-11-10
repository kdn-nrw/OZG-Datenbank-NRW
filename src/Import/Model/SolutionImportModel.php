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

use App\Entity\FormServerSolution;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;
use App\Import\Annotation\ImportModelAnnotation;

/**
 * Class SolutionImportModel
 * @ImportModelAnnotation(targetEntity="\App\Entity\Solution")
 */
class SolutionImportModel extends AbstractImportModel
{
    /**
     * @var int|null
     *
     * @ImportModelAnnotation(parameter="id", dataType="int", required=true)
     */
    protected $importId;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="name", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var SpecializedProcedure[]|null
     *
     * @ImportModelAnnotation(parameter="fachverfahren", dataType="collection", required=false, targetEntity="\App\Entity\SpecializedProcedure", mapToProperty="name")
     */
    protected $specializedProcedures;

    /**
     * @var Solution[]|null
     *
     * @ImportModelAnnotation(parameter="leika_id", dataType="collection", required=false, targetEntity="\App\Entity\Solution", mapToProperty="name")
     */
    protected $serviceSolutions;

    /**
     * @var FormServerSolution|null
     *
     * @ImportModelAnnotation(parameter="artikelnummer", dataType="string", required=true, mapToEntity="\App\Entity\FormServerSolution", mapToProperty="articleNumber")
     */
    protected $articleNumber;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="assistententyp", dataType="string", required=false, mapToEntity="\App\Entity\FormServerSolution")
     */
    protected $assistantType;

    /**
     * @var string|null
     *
     * @ImportModelAnnotation(parameter="identifikationsnummer", dataType="string", required=false, mapToEntity="\App\Entity\FormServerSolution")
     */
    protected $articleKey;

    /**
     * @var bool|null
     *
     * @ImportModelAnnotation(parameter="druckvorlage_geeignet", dataType="string", required=false, mapToEntity="\App\Entity\FormServerSolution")
     */
    protected $usableAsPrintTemplate;

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
     * @return Solution[]|null
     */
    public function getServiceSolutions(): ?array
    {
        return $this->serviceSolutions;
    }

    /**
     * @param Solution[]|null $serviceSolutions
     */
    public function setServiceSolutions(?array $serviceSolutions): void
    {
        $this->serviceSolutions = $serviceSolutions;
    }

    /**
     * @return FormServerSolution|null
     */
    public function getArticleNumber(): ?FormServerSolution
    {
        return $this->articleNumber;
    }

    /**
     * @param FormServerSolution|null $articleNumber
     */
    public function setArticleNumber(?FormServerSolution $articleNumber): void
    {
        $this->articleNumber = $articleNumber;
    }

    /**
     * @return string|null
     */
    public function getAssistantType(): ?string
    {
        return $this->assistantType;
    }

    /**
     * @param string|null $assistantType
     */
    public function setAssistantType(?string $assistantType): void
    {
        $this->assistantType = $assistantType;
    }

    /**
     * @return string|null
     */
    public function getArticleKey(): ?string
    {
        return $this->articleKey;
    }

    /**
     * @param string|null $articleKey
     */
    public function setArticleKey(?string $articleKey): void
    {
        $this->articleKey = $articleKey;
    }

    /**
     * @return bool|null
     */
    public function getUsableAsPrintTemplate(): ?bool
    {
        return $this->usableAsPrintTemplate;
    }

    /**
     * @param bool|null $usableAsPrintTemplate
     */
    public function setUsableAsPrintTemplate(?bool $usableAsPrintTemplate): void
    {
        $this->usableAsPrintTemplate = $usableAsPrintTemplate;
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
