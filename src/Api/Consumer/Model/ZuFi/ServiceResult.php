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

class ServiceResult extends AbstractResult
{
    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="leistungsbezeichnung", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="kurztext", dataType="string", required=false)
     */
    protected $teaser;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="Volltext", dataType="string", required=false)
     */
    protected $description;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="schluessel", dataType="string", required=true)
     */
    protected $serviceKey;

    /**
     * @var array
     * @ApiSearchModelAnnotation(parameter="begriffeImKontext", dataType="array", required=true)
     */
    protected $synonyms = [];

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="urheber", dataType="string", required=true)
     */
    protected $author;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="rechtsgrundlage", dataType="string", required=true)
     */
    protected $legalBasis;

    /**
     * @var ResultCollection|UriResult[]
     * @ApiSearchModelAnnotation(parameter="rechtsgrundlageLinks", modelClass="App\Api\Consumer\Model\ZuFi\UriResult", dataType="collection", required=false)
     */
    protected $legalBasisUris;

    public function __construct()
    {
        $this->legalBasisUris = new ResultCollection();
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
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @param string|null $author
     */
    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    /**
     * @param string|null $legalBasis
     */
    public function setLegalBasis(?string $legalBasis): void
    {
        $this->legalBasis = $legalBasis;
    }

    /**
     * @return string|null
     */
    public function getLegalBasis(): ?string
    {
        return $this->legalBasis;
    }

    /**
     * @return ResultCollection|UriResult[]
     */
    public function getLegalBasisUris(): ResultCollection
    {
        return $this->legalBasisUris;
    }

    /**
     * @param ResultCollection|UriResult[] $legalBasisUris
     */
    public function setLegalBasisUris(ResultCollection $legalBasisUris): void
    {
        $this->legalBasisUris = $legalBasisUris;
    }

    /**
     * @param UriResult $legalBasisUri
     */
    public function addLegalBasisUri(UriResult $legalBasisUri): void
    {
        $this->legalBasisUris->add($legalBasisUri);
    }

    /**
     * Returns the import key data
     *
     * @return array|null
     */
    public function getImportKeyData(): ?array
    {
        return [
            'serviceKey' => $this->getServiceKey(),
        ];
    }

    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getServiceKey();
    }
}
