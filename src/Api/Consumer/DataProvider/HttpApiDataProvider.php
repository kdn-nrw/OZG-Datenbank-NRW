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

namespace App\Api\Consumer\DataProvider;

use App\Api\Consumer\InvalidParametersException;
use App\Entity\Api\ApiConsumer as ApiConsumerEntity;
use App\Import\Annotation\InjectAnnotationReaderTrait;
use App\Import\DataProcessor\DataProcessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpApiDataProvider extends AbstractDemandDataProvider
{
    use InjectAnnotationReaderTrait;

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var ApiConsumerEntity
     */
    protected $apiConsumerEntity;

    public function __construct(HttpClientInterface $client)
    {
        // https://symfony.com/doc/4.4/http_client.html#installation
        $this->client = $client;
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
        $demandModelConfiguration = $this->getDemandPropertyConfiguration();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $parameters = [];
        foreach ($demandModelConfiguration as $property => $configuration) {
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
        $apiBaseUrl = $this->getApiConsumerEntity()->getUrl();
        return $apiBaseUrl . '?' . $this->buildQueryString();
    }

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
     * Load and process the data
     *
     * @param DataProcessorInterface $dataProcessor
     * @return int The number of records loaded
     */
    public function process(DataProcessorInterface $dataProcessor): int
    {
        $demand = $this->getDemand();
        $options = [
            'timeout' => 10,
            'verify_peer' => 0,
            'verify_host' => 0,
        ];
        if (null !== $proxy = $this->getApiConsumerEntity()->getProxy()) {
            $options['proxy'] = str_replace(['http://', 'https://'], '', $proxy);
        }
        $response = $this->client->request($demand->getRequestMethod(), $this->buildQueryUrl(), $options);
        return $this->processResponse($dataProcessor, $response);
    }

    /**
     * @param DataProcessorInterface $dataProcessor
     * @param ResponseInterface $response
     * @return int The number of found rows
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function processResponse(DataProcessorInterface $dataProcessor, ResponseInterface $response): int
    {
        //$statusCode = $response->getStatusCode();
        $data = $this->getRawResultRows($response, $dataProcessor);
        $rowNr = 0;
        if (!empty($data)) {
            foreach ($data as $row) {
                ++$rowNr;
                $dataProcessor->addRecordRaw($row, $rowNr);
            }
        }
        return $rowNr;
    }

    /**
     * @param ResponseInterface $response
     * @param DataProcessorInterface $dataProcessor
     * @return mixed|array Returns the raw result rows
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getRawResultRows(ResponseInterface $response, DataProcessorInterface $dataProcessor)
    {
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $data = [];
        if (strpos($contentType, 'application/json') === 0) {
            $data = json_decode($content, true);
        }
        $results = $dataProcessor->getResultCollection();
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
            $dataProcessor->addBaseResultData($data);
        } else {
            $rows = $data;
        }
        return $rows;
    }

    /**
     * @return ApiConsumerEntity
     */
    public function getApiConsumerEntity(): ApiConsumerEntity
    {
        return $this->apiConsumerEntity;
    }

    /**
     * @param ApiConsumerEntity $apiConsumerEntity
     */
    public function setApiConsumerEntity(ApiConsumerEntity $apiConsumerEntity): void
    {
        $this->apiConsumerEntity = $apiConsumerEntity;
    }

}
