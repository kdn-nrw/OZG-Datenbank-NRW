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
use App\Api\Consumer\Model\AbstractResult;
use App\Api\Consumer\Model\ResultCollection;
use App\Util\SnakeCaseConverter;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractApiConsumer implements ApiConsumerInterface
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var AbstractDemand
     */
    protected $demand;

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
        return SnakeCaseConverter::classNameToSnakeCase(str_replace('Consumer', '', $reflect->getShortName()));
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
        $propertyConfiguration = $this->getPropertyConfiguration();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $parameters = [];
        foreach ($propertyConfiguration as $property => $configuration) {
            $value = $propertyAccessor->getValue($demand, $property);
            if (null !== $value) {
                $key = $configuration->getParameter();
                $parameters[$key] = $value;
            } elseif ($configuration->isRequired()) {
                throw new InvalidParametersException(sprintf('Required parameter %s is missing', $property));
            }
        }
        if (empty($parameters)) {
            throw new InvalidParametersException('No parameters set for search');
        }
        foreach ($parameters as $key => $value) {
            $query .= ($query !== '' ? '&' : '') . $key . '=' . urlencode($value);
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
     * Returns the query url
     *
     * @return string
     */
    public function getQueryUrl(): string
    {
        return $this->buildQueryUrl();
    }

    /**
     * Searches for the submitted demand values and returns the search result
     *
     * @return ResultCollection The result content
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function search(): ResultCollection
    {
        $demand = $this->getDemand();
        $response = $this->client->request($demand->getRequestMethod(), $this->buildQueryUrl());
        return $this->processResponse($response);
    }

    /**
     * Returns the API demand instance
     * @param string|null $query
     * @return AbstractDemand
     */
    public function getDemand(?string $query = null): AbstractDemand
    {
        if (null === $this->demand) {
            $demandClass = $this->getDemandClass();
            $this->demand = new $demandClass();
            if ($query) {
                $propertyConfiguration = $this->getModelPropertyConfiguration($demandClass);
                $propertyAccessor = PropertyAccess::createPropertyAccessor();
                $queryParamaters = explode('|', $query);
                $offset = 0;
                foreach ($propertyConfiguration as $property => $configuration) {
                    if (!empty($queryParamaters[$offset])
                        && $configuration->isSearchProperty()
                        && $propertyAccessor->isWritable($this->demand, $property)) {
                        $value = str_replace('$$', '|', $queryParamaters[$offset]);
                        $propertyAccessor->setValue($this->demand, $property, $value);
                    }
                    ++$offset;
                }
            }
        }
        return $this->demand;
    }

    /**
     * Returns the value string for the demand query
     *
     * @return string
     */
    public function getDemandValueString(): string
    {
        $values = [];
        $demand = $this->getDemand();
        $propertyConfiguration = $this->getModelPropertyConfiguration($demand);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($propertyConfiguration as $property => $configuration) {
            if ($configuration->isSearchProperty()) {
                if ($propertyAccessor->isWritable($this->demand, $property)) {
                    $value = $propertyAccessor->getValue($this->demand, $property);
                    $values[$property] = str_replace('|', '$$', $value);
                } else {
                    $values[$property] = '';
                }
            }
        }
        return implode('|', $values);
    }

    /**
     * @param ResponseInterface $response
     * @return AbstractResult[]|ResultCollection
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function processResponse(ResponseInterface $response): ResultCollection
    {
        $results = new ResultCollection();
        //$statusCode = $response->getStatusCode();
        $modelClass = $this->getResultModelClass();
        $propertyConfiguration = $this->getModelPropertyConfiguration($modelClass);
        $data = $this->getRawResultRows($response, $results);
        if (!empty($data)) {
            foreach ($data as $row) {
                $result = $this->mapRowToModel($propertyConfiguration, $modelClass, $row);
                $results->add($result);
            }
        }
        if ($results->getTotalResultCount() === 0) {
            $results->setTotalResultCount(count($results));
        }
        return $results;
    }

    /**
     * Map the given api result row to the result model
     *
     * @param array $propertyConfiguration The result model property mapping configuration
     * @param string $modelClass The result model class
     * @param array $row The api result row
     * @return AbstractResult
     */
    protected function mapRowToModel(array $propertyConfiguration, string $modelClass, array $row): AbstractResult
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        /** @var AbstractResult $result */
        $result = new $modelClass();
        $unmappedData = $row;
        $mapKeys = [];
        foreach (array_keys($row) as $key) {
            $mapKeys[mb_strtolower($key)] = $key;
        }
        foreach ($propertyConfiguration as $property => $configuration) {
            /** @var ApiSearchModelAnnotation $configuration */
            $parameter = mb_strtolower($configuration->getParameter());
            $pKey = $mapKeys[$parameter] ?? null;
            if ($pKey) {
                $dataType = $configuration->getDataType();
                switch ($dataType) {
                    case ApiSearchModelAnnotation::DATA_TYPE_FLOAT:
                        $value = (float)$row[$pKey];
                        break;
                    case ApiSearchModelAnnotation::DATA_TYPE_INT:
                        $value = (int)$row[$pKey];
                        break;
                    case ApiSearchModelAnnotation::DATA_TYPE_MODEL:
                        $refModelClass = $configuration->getModelClass();
                        if ($refModelClass && !empty($row[$pKey])) {
                            $value = $this->mapRowToModel(
                                $this->getModelPropertyConfiguration($refModelClass),
                                $refModelClass,
                                $row[$pKey]
                            );
                        } else {
                            $value = null;
                        }
                        break;
                    case ApiSearchModelAnnotation::DATA_TYPE_MODEL_COLLECTION:
                        $refModelClass = $configuration->getModelClass();
                        $value = $propertyAccessor->getValue($result, $property);
                        if ($refModelClass && !empty($row[$pKey])) {
                            /** @var ResultCollection $value */
                            $refPropertyConfiguration = $this->getModelPropertyConfiguration($refModelClass);
                            foreach ($row[$pKey] as $refRow) {
                                $refModel = $this->mapRowToModel(
                                    $refPropertyConfiguration,
                                    $refModelClass,
                                    $refRow
                                );
                                $value->add($refModel);
                            }
                            $value->setTotalResultCount(count($value));
                        }
                        break;
                    default:
                        $value = $row[$pKey];
                        break;
                }
                $propertyAccessor->setValue($result, $property, $value);
                unset($unmappedData[$pKey]);
            }
        }
        $result->setUnmappedData($unmappedData);
        return $result;
    }

    /**
     * @param ResponseInterface $response
     * @param ResultCollection $results
     * @return mixed|array Returns the raw result rows
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getRawResultRows(ResponseInterface $response, ResultCollection $results)
    {
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $data = [];
        if (strpos($contentType, 'application/json') === 0) {
            $data = json_decode($content, true);
        }
        if (!empty($data['paginierung'])) {
            $paginationInfo = $data['paginierung'];
            $results->setOffset((int)$paginationInfo['start']);
            $results->setTotalResultCount((int)$paginationInfo['gesamt']);
            $results->setResultsPerPage((int)$paginationInfo['anzahl']);
            $results->setPage((int)$paginationInfo['aktuelleSeite']);
            unset($data['paginierung']);
        }
        if (array_key_exists('daten', $data)) {
            $rows = $data['daten'];
            unset($data['daten']);
            $results->setUnmappedData($data);
        } else {
            $rows = $data;
        }
        return $rows;
    }

    /**
     * Returns the api demand model configuration
     *
     * @return ApiSearchModelAnnotation[]|array
     */
    public function getPropertyConfiguration(): array
    {
        return $this->getModelPropertyConfiguration($this->getDemandClass());
    }

    /**
     * Returns the api form model annotations
     *
     * @param mixed $model Either a string containing the name of the class to reflect, or an object.
     * @return ApiSearchModelAnnotation[]|array
     * @throws ReflectionException
     */
    protected function getModelPropertyConfiguration($model): array
    {
        $annotations = [];
        $annotationReader = new CachedReader(new AnnotationReader(), new ArrayCache());
        $reflectionClass = new ReflectionClass($model);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $apiModelAnnotation = $annotationReader->getPropertyAnnotation($property, ApiSearchModelAnnotation::class);
            if (null !== $apiModelAnnotation) {
                /** @var ApiSearchModelAnnotation $apiModelAnnotation */
                $annotations[$property->getName()] = $apiModelAnnotation;
            }
        }
        return $annotations;
    }
}
