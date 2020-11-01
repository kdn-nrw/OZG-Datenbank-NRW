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

class LeikaResult extends AbstractResult
{
    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="Bezeichnung", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="Schluessel", dataType="string", required=true)
     */
    protected $serviceKey;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="Kurztext", dataType="string", required=false)
     */
    protected $teaser;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="Volltext", dataType="string", required=false)
     */
    protected $description;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="Verfahrensablauf", dataType="string", required=false)
     */
    protected $procedure;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="Erforderliche Unterlagen", dataType="string", required=false)
     */
    protected $requiredDocuments;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="Besondere Merkmale", dataType="string", required=false)
     */
    protected $specialFeatures;

    /**
     * @var array
     * @ApiSearchModelAnnotation(parameter="Synonyme", dataType="array", required=false)
     */
    protected $synonyms = [];

    /**
     * @var float|null
     * @ApiSearchModelAnnotation(parameter="confidence", dataType="float", required=false)
     */
    protected $confidence;

    /**
     * @var int|null
     * @ApiSearchModelAnnotation(parameter="Typisierung", dataType="int", required=false)
     */
    protected $type;

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
    public function getServiceKey(): ?string
    {
        return $this->serviceKey;
    }

    /**
     * @param string|null $serviceKey
     */
    public function setServiceKey(?string $serviceKey): void
    {
        $this->serviceKey = $serviceKey;
    }

    /**
     * @return string|null
     */
    public function getTeaser(): ?string
    {
        return $this->teaser;
    }

    /**
     * @param string|null $teaser
     */
    public function setTeaser(?string $teaser): void
    {
        $this->teaser = $teaser;
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
    public function getProcedure(): ?string
    {
        return $this->procedure;
    }

    /**
     * @param string|null $procedure
     */
    public function setProcedure(?string $procedure): void
    {
        $this->procedure = $procedure;
    }

    /**
     * @return string|null
     */
    public function getRequiredDocuments(): ?string
    {
        return $this->requiredDocuments;
    }

    /**
     * @param string|null $requiredDocuments
     */
    public function setRequiredDocuments(?string $requiredDocuments): void
    {
        $this->requiredDocuments = $requiredDocuments;
    }

    /**
     * @return string|null
     */
    public function getSpecialFeatures(): ?string
    {
        return $this->specialFeatures;
    }

    /**
     * @param string|null $specialFeatures
     */
    public function setSpecialFeatures(?string $specialFeatures): void
    {
        $this->specialFeatures = $specialFeatures;
    }

    /**
     * @return array
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }

    /**
     * @param array $synonyms
     */
    public function setSynonyms(array $synonyms): void
    {
        $this->synonyms = array_filter($synonyms);
    }

    /**
     * @return float|null
     */
    public function getConfidence(): ?float
    {
        return $this->confidence;
    }

    /**
     * @param float|null $confidence
     */
    public function setConfidence(?float $confidence): void
    {
        $this->confidence = $confidence;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getServiceKey();
    }
}
