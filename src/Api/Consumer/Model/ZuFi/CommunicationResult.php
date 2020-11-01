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

class CommunicationResult extends AbstractResult
{
    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="kanal", dataType="string", required=true)
     */
    protected $channel;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="kennung", dataType="string", required=true)
     */
    protected $uri;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="kennungzusatz", dataType="string", required=false)
     */
    protected $uriAddition;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="zusatz", dataType="string", required=false)
     */
    protected $addition;

    /**
     * @return string|null
     */
    public function getChannel(): ?string
    {
        return $this->channel;
    }

    /**
     * @param string|null $channel
     */
    public function setChannel(?string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @param string|null $uri
     */
    public function setUri(?string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @return string|null
     */
    public function getUriAddition(): ?string
    {
        return $this->uriAddition;
    }

    /**
     * @param string|null $uriAddition
     */
    public function setUriAddition(?string $uriAddition): void
    {
        $this->uriAddition = $uriAddition;
    }

    /**
     * @return string|null
     */
    public function getAddition(): ?string
    {
        return $this->addition;
    }

    /**
     * @param string|null $addition
     */
    public function setAddition(?string $addition): void
    {
        $this->addition = $addition;
    }

    public function __toString()
    {
        return ucfirst($this->getChannel()) . ': ' . $this->getUri();
    }
}
