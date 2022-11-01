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

/**
 * Class ServiceResult
 * @ApiSearchModelAnnotation(targetEntity="\App\Entity\Api\ServiceBaseResult")
 */
class ServiceResult extends ServiceBaseResult
{
    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="leistungsbezeichnung", dataType="string", required=true)
     */
    protected $name;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="leistungsbezeichnung2", dataType="string", required=false)
     */
    protected $name2;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="datum", dataType="string", required=false, disableImport=true)
     */
    protected $date;

    /**
     * @var array
     * @ApiSearchModelAnnotation(parameter="begriffeImKontext", dataType="array", required=true)
     */
    protected $synonyms = [];

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="erforderlicheUnterlagen", dataType="string", required=false)
     */
    protected $requiredDocuments;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="fachlichFreigegebenAm", dataType="string", required=false)
     */
    protected $technicallyApprovedAt;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="fachlichFreigegebenDurch", dataType="string", required=false)
     */
    protected $technicallyApprovedBy;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="urheber", dataType="string", required=false, mapToProperty="communeAuthor")
     */
    protected $author;

    /**
     * @var bool|null
     * @ApiSearchModelAnnotation(parameter="wspRelevanz", dataType="bool", required=false, mapToProperty="communeWspRelevance")
     */
    protected $wspRelevance;

    /**
     * Disable import because DateTime conversion is required!
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="letzteAktualisierung", dataType="string", required=false, disableImport=true)
     */
    protected $lastUpdatedAt;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="urlOnlineDienst", dataType="string", required=false)
     */
    protected $urlOnlineService;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="urlInfo", dataType="string", required=false, mapToProperty="communeOnlineServiceUrlInfo")
     */
    protected $urlOnlineServiceInfo;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="weiterfuehrendeInformationen", dataType="string", required=false)
     */
    protected $furtherInformation;

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
     * @return bool|null
     */
    public function getWspRelevance(): ?bool
    {
        return $this->wspRelevance;
    }

    /**
     * @param bool|null $wspRelevance
     */
    public function setWspRelevance(?bool $wspRelevance): void
    {
        $this->wspRelevance = $wspRelevance;
    }

    /**
     * @return string|null
     */
    public function getLastUpdatedAt(): ?string
    {
        return $this->lastUpdatedAt;
    }

    /**
     * @param string|null $lastUpdatedAt
     */
    public function setLastUpdatedAt(?string $lastUpdatedAt): void
    {
        $this->lastUpdatedAt = $lastUpdatedAt;
    }

    /**
     * @return string|null
     */
    public function getUrlOnlineServiceInfo(): ?string
    {
        return $this->urlOnlineServiceInfo;
    }

    /**
     * @param string|null $urlOnlineServiceInfo
     */
    public function setUrlOnlineServiceInfo(?string $urlOnlineServiceInfo): void
    {
        $this->urlOnlineServiceInfo = $urlOnlineServiceInfo;
    }

}
