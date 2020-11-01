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

namespace App\Api\Consumer;

use App\Api\Annotation\ApiSearchModelAnnotation;
use App\Api\Consumer\Model\AbstractDemand;
use App\Api\Consumer\Model\ResultCollection;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

interface ApiConsumerInterface
{
    /**
     * Returns the key for the provider instance
     * @return string
     */
    public function getKey(): string;

    /**
     * Returns the name of the API
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the description for the API
     * @return string
     */
    public function getDescription(): string;

    /**
     * Returns the query url
     *
     * @return string
     */
    public function getQueryUrl(): string;

    /**
     * Returns the API demand instance
     * @param string|null $query
     * @return AbstractDemand
     */
    public function getDemand(?string $query = null): AbstractDemand;

    /**
     * Returns the value string for the demand query
     *
     * @return string
     */
    public function getDemandValueString(): string;

    /**
     * Returns the class name for the demand model
     *
     * @return string
     */
    public function getDemandClass(): string;

    /**
     * Searches for the submitted demand values and returns the search result
     *
     * @return ResultCollection The result content
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function search(): ResultCollection;

    /**
     * Returns the api form model annotations
     *
     * @return ApiSearchModelAnnotation[]|array
     */
    public function getPropertyConfiguration(): array;

    /**
     * Returns the search result template for this consumer
     * @return string
     */
    public function getResultTemplate(): string;

    /**
     * Returns the class name for the search form type
     *
     * @return string
     */
    public function getFormTypeClass(): string;

    /**
     * Returns the class name for the result model
     *
     * @return string
     */
    public function getResultModelClass(): string;
}
