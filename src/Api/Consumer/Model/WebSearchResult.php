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
use Symfony\Component\Mime\MimeTypes;

class WebSearchResult extends AbstractResult
{

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="id", dataType="string", required=true)
     */
    protected $id;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="titel", dataType="string", required=true)
     */
    protected $title;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="snippet", dataType="string", required=true)
     */
    protected $snippet;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="url", dataType="string", required=true)
     */
    protected $url;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="kodierteUrl", dataType="string", required=false)
     */
    protected $encodedUrl;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="kodierung", dataType="string", required=false)
     */
    protected $encoding;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="groesse", dataType="int", required=false)
     */
    protected $size;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="mime", dataType="string", required=false)
     */
    protected $mime;

    /**
     * The date values returned from the API are mostly invalid.
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="crawlDatum", dataType="string", required=false)
     */
    protected $crawlDate;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="datum", dataType="string", required=false)
     */
    protected $date;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
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
    public function getSnippet(): ?string
    {
        return $this->snippet;
    }

    /**
     * @param string|null $snippet
     */
    public function setSnippet(?string $snippet): void
    {
        if ($snippet) {
            // Remove unicode whitespaces
            $snippet = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $snippet);
        }
        $this->snippet = trim(strip_tags($snippet));
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
    public function getEncodedUrl(): ?string
    {
        return $this->encodedUrl;
    }

    /**
     * @param string|null $encodedUrl
     */
    public function setEncodedUrl(?string $encodedUrl): void
    {
        $this->encodedUrl = $encodedUrl;
    }

    /**
     * @return string|null
     */
    public function getEncoding(): ?string
    {
        return $this->encoding;
    }

    /**
     * @param string|null $encoding
     */
    public function setEncoding(?string $encoding): void
    {
        $this->encoding = $encoding;
    }

    /**
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * @param string|null $size
     */
    public function setSize(?string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string|null
     */
    public function getMime(): ?string
    {
        return $this->mime;
    }

    /**
     * @param string|null $mime
     */
    public function setMime(?string $mime): void
    {
        $this->mime = $mime;
    }

    /**
     * @return string|null
     */
    public function getCrawlDate(): ?string
    {
        return $this->crawlDate;
    }

    /**
     * @param string|null $crawlDate
     */
    public function setCrawlDate(?string $crawlDate): void
    {
        $this->crawlDate = $crawlDate;
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
     * Processes and formats the result date, because the API returns invalid dates
     * @return string
     */
    public function getFormattedDate(): string
    {
        if ($this->date) {
            $dateParts = explode('-', $this->date);
            // Check if date is invalid
            $yearVal = (int) ltrim($dateParts[0], '0');
            if ($yearVal < 2000) {
                $year = (int) date('Y');
                $day = $yearVal;
                $month = (int) date('n');
                if ($day > (int) date('j')) {
                    --$month;
                    if ($month === 0) {
                        --$year;
                        $month = 12;
                    }
                }
                $tstamp = mktime(0, 0, 0, $month, $day, $year);
            } else {
                $tstamp = strtotime($this->date);
            }
            if ($tstamp) {
                return date('d.m.Y', $tstamp);
            }
        }
        return '';
    }

    /**
     * @return array
     */
    public function getUnmappedData(): array
    {
        if ($this->size) {
            $base = log($this->size, 1024);
            $suffixes = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            $formattedSize = round((1024 ** ($base - floor($base))));
            $this->unmappedData['Größe'] = $formattedSize . ' ' . $suffixes[floor($base)];
        }
        if ($this->mime && class_exists(MimeTypes::class)) {
            $mimeTypes = new MimeTypes();
            $extensions = $mimeTypes->getExtensions($this->mime);
            $this->unmappedData['Typ'] = strtoupper(implode(', ', $extensions));
        }
        return $this->unmappedData;
    }

    public function __toString()
    {
        $title = trim((string) $this->getTitle());
        if (strlen($title) < 4) {
            $title = (string) $this->getUrl();
        }
        return $title;
    }

}
