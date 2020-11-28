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

/**
 * Class ServiceBaseResult
 * @ApiSearchModelAnnotation(targetEntity="\App\Entity\Api\ServiceBaseResult")
 */
class ServiceBaseResult extends AbstractResult
{

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="schluessel", dataType="string", required=true)
     */
    protected $serviceKey;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="bezeichnung", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="gruppierung", dataType="string", required=false)
     */
    protected $group;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="kennung", dataType="string", required=false)
     */
    protected $callSign;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="verrichtung", dataType="string", required=false)
     */
    protected $performance;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="verrichtungsdetail", dataType="string", required=false)
     */
    protected $performanceDetail;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="bezeichnung2", dataType="string", required=false)
     */
    protected $name2;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="typisierung", dataType="string", required=false)
     */
    protected $type;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="typ", dataType="string", required=false)
     */
    protected $serviceType;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="datum", dataType="string", required=false)
     */
    protected $date;

    /**
     * @var array
     * @ApiSearchModelAnnotation(parameter="besondere_merkmale", dataType="array", required=false)
     */
    protected $specialFeatures = [];

    /**
     * @var array
     * @ApiSearchModelAnnotation(parameter="synonyme", dataType="array", required=false)
     */
    protected $synonyms = [];

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="kurztext", dataType="string", required=false)
     */
    protected $shortText;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="volltext", dataType="string", required=false)
     */
    protected $description;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="rechtsgrundlage", dataType="string", required=false)
     */
    protected $legalBasis;

    /**
     * @var ResultCollection|UriResult[]
     * @ApiSearchModelAnnotation(parameter="rechtsgrundlageLinks", modelClass="App\Api\Consumer\Model\ZuFi\UriResult", dataType="collection", required=false)
     */
    protected $legalBasisUris;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="erforderliche_unterlagen", dataType="string", required=false)
     */
    protected $requiredDocuments;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="voraussetzungen", dataType="string", required=false)
     */
    protected $requirements;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="kosten", dataType="string", required=false)
     */
    protected $costs;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="bearbeitungsdauer", dataType="string", required=false)
     */
    protected $processingTime;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="verfahrensablauf", dataType="string", required=false)
     */
    protected $processFlow;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="fristen", dataType="string", required=false)
     */
    protected $deadlines;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="formulare", dataType="string", required=false)
     */
    protected $forms;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="weiterfuehrende_informationen", dataType="string", required=false)
     */
    protected $furtherInformation;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="urlOnlineDienst", dataType="string", required=false)
     */
    protected $urlOnlineService;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="teaser", dataType="string", required=false)
     */
    protected $teaser;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="ansprechpunkt", dataType="string", required=false)
     */
    protected $pointOfContact;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="fachlich_freigegeben_am", dataType="string", required=false)
     */
    protected $technicallyApprovedAt;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="fachlich_freigegeben_durch", dataType="string", required=false)
     */
    protected $technicallyApprovedBy;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="hinweise", dataType="string", required=false)
     */
    protected $hints;

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
     * @param array|null $synonyms
     */
    public function setSynonyms(?array $synonyms): void
    {
        $this->synonyms = null !== $synonyms ? array_filter($synonyms) : [];
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
     * @param ResultCollection|UriResult[]|null $legalBasisUris
     */
    public function setLegalBasisUris(?ResultCollection $legalBasisUris): void
    {
        if (null !== $legalBasisUris) {
            $this->legalBasisUris = $legalBasisUris;
        }
    }

    /**
     * @param UriResult $legalBasisUri
     */
    public function addLegalBasisUri(UriResult $legalBasisUri): void
    {
        $this->legalBasisUris->add($legalBasisUri);
    }

    /**
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * @param string|null $group
     */
    public function setGroup(?string $group): void
    {
        $this->group = $group;
    }

    /**
     * @return string|null
     */
    public function getCallSign(): ?string
    {
        return $this->callSign;
    }

    /**
     * @param string|null $callSign
     */
    public function setCallSign(?string $callSign): void
    {
        $this->callSign = $callSign;
    }

    /**
     * @return string|null
     */
    public function getPerformance(): ?string
    {
        return $this->performance;
    }

    /**
     * @param string|null $performance
     */
    public function setPerformance(?string $performance): void
    {
        $this->performance = $performance;
    }

    /**
     * @return string|null
     */
    public function getPerformanceDetail(): ?string
    {
        return $this->performanceDetail;
    }

    /**
     * @param string|null $performanceDetail
     */
    public function setPerformanceDetail(?string $performanceDetail): void
    {
        $this->performanceDetail = $performanceDetail;
    }

    /**
     * @return string|null
     */
    public function getName2(): ?string
    {
        return $this->name2;
    }

    /**
     * @param string|null $name2
     */
    public function setName2(?string $name2): void
    {
        $this->name2 = $name2;
    }

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
    public function getServiceType(): ?string
    {
        return $this->serviceType;
    }

    /**
     * @param string|null $serviceType
     */
    public function setServiceType(?string $serviceType): void
    {
        $this->serviceType = $serviceType;
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * @param string|null $date
     */
    public function setDate(?string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function getSpecialFeatures(): array
    {
        return $this->specialFeatures;
    }

    /**
     * @param array|null $specialFeatures
     */
    public function setSpecialFeatures(?array $specialFeatures): void
    {
        $this->specialFeatures = $specialFeatures ?? [];
    }

    /**
     * @return string|null
     */
    public function getShortText(): ?string
    {
        return $this->shortText;
    }

    /**
     * @param string|null $shortText
     */
    public function setShortText(?string $shortText): void
    {
        $this->shortText = $shortText;
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
    public function getRequirements(): ?string
    {
        return $this->requirements;
    }

    /**
     * @param string|null $requirements
     */
    public function setRequirements(?string $requirements): void
    {
        $this->requirements = $requirements;
    }

    /**
     * @return string|null
     */
    public function getCosts(): ?string
    {
        return $this->costs;
    }

    /**
     * @param string|null $costs
     */
    public function setCosts(?string $costs): void
    {
        $this->costs = $costs;
    }

    /**
     * @return string|null
     */
    public function getProcessingTime(): ?string
    {
        return $this->processingTime;
    }

    /**
     * @param string|null $processingTime
     */
    public function setProcessingTime(?string $processingTime): void
    {
        $this->processingTime = $processingTime;
    }

    /**
     * @return string|null
     */
    public function getProcessFlow(): ?string
    {
        return $this->processFlow;
    }

    /**
     * @param string|null $processFlow
     */
    public function setProcessFlow(?string $processFlow): void
    {
        $this->processFlow = $processFlow;
    }

    /**
     * @return string|null
     */
    public function getDeadlines(): ?string
    {
        return $this->deadlines;
    }

    /**
     * @param string|null $deadlines
     */
    public function setDeadlines(?string $deadlines): void
    {
        $this->deadlines = $deadlines;
    }

    /**
     * @return string|null
     */
    public function getForms(): ?string
    {
        return $this->forms;
    }

    /**
     * @param string|null $forms
     */
    public function setForms(?string $forms): void
    {
        $this->forms = $forms;
    }

    /**
     * @return string|null
     */
    public function getFurtherInformation(): ?string
    {
        return $this->furtherInformation;
    }

    /**
     * @param string|null $furtherInformation
     */
    public function setFurtherInformation(?string $furtherInformation): void
    {
        $this->furtherInformation = $furtherInformation;
    }

    /**
     * @return string|null
     */
    public function getUrlOnlineService(): ?string
    {
        return $this->urlOnlineService;
    }

    /**
     * @param string|null $urlOnlineService
     */
    public function setUrlOnlineService(?string $urlOnlineService): void
    {
        $this->urlOnlineService = $urlOnlineService;
    }

    /**
     * @return string|null
     */
    public function getPointOfContact(): ?string
    {
        return $this->pointOfContact;
    }

    /**
     * @param string|null $pointOfContact
     */
    public function setPointOfContact(?string $pointOfContact): void
    {
        $this->pointOfContact = $pointOfContact;
    }

    /**
     * @return string|null
     */
    public function getTechnicallyApprovedAt(): ?string
    {
        return $this->technicallyApprovedAt;
    }

    /**
     * @param string|null $technicallyApprovedAt
     */
    public function setTechnicallyApprovedAt(?string $technicallyApprovedAt): void
    {
        $this->technicallyApprovedAt = $technicallyApprovedAt;
    }

    /**
     * @return string|null
     */
    public function getTechnicallyApprovedBy(): ?string
    {
        return $this->technicallyApprovedBy;
    }

    /**
     * @param string|null $technicallyApprovedBy
     */
    public function setTechnicallyApprovedBy(?string $technicallyApprovedBy): void
    {
        $this->technicallyApprovedBy = $technicallyApprovedBy;
    }

    /**
     * @return string|null
     */
    public function getHints(): ?string
    {
        return $this->hints;
    }

    /**
     * @param string|null $hints
     */
    public function setHints(?string $hints): void
    {
        $this->hints = $hints;
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
