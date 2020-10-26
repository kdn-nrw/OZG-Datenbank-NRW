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

namespace App\Service\Zsm\Api;

use App\Util\SnakeCaseConverter;
use ReflectionClass;
use ReflectionException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractApiProvider implements ApiProviderInterface
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        // https://symfony.com/doc/4.4/http_client.html#installation
        $this->client = $client;
    }

    /**
     * Returns the key for the provider instance
     *
     * @return string
     * @throws ReflectionException
     */
    public function getKey(): string
    {
        $reflect = new ReflectionClass($this);
        return SnakeCaseConverter::classNameToSnakeCase($reflect->getShortName());
    }

    /**
     * Builds the query string from the given list of valid parameters and the search values
     *
     * @return string
     * @throws InvalidParametersException If required parameter is missing
     */
    private function buildQueryString(): string
    {
        $query = '';
        $demand = $this->getDemand();
        $searchValues = $demand->getValues();
        $parameterList = $demand->getParameterRegistry();
        foreach ($parameterList as $parameterInstance) {
            $key = $parameterInstance->getName();
            if (array_key_exists($key, $searchValues)) {
                $query .= ($query !== '' ? '&' : '') . $key . '=' . urlencode($searchValues[$key]);
            } elseif ($parameterInstance->isRequired()) {
                throw new InvalidParametersException(sprintf('Required parameter %s is missing', $key));
            }
        }
        return $query;
    }

    /**
     * @return string
     */
    private function buildQueryUrl(): string
    {
        return $this->getApiUrl() . '?' . $this->buildQueryString();
    }

    abstract protected function getApiUrl(): string;

    /**
     * Fetches the query result
     *
     * @return string The result content
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fetch(): string
    {
        $demand = $this->getDemand();
        $response = $this->client->request(
            $demand->getRequestMethod(),
            $this->buildQueryUrl()
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        echo '<pre>$contentType: ' . print_r($contentType, true) . '</pre>';
        echo '<pre>$content: ' . print_r($content, true) . '</pre>';
        die('TEST');//TODO: DEBUG
        return $content;
    }
}
