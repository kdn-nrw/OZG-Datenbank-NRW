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
use App\Api\Consumer\DataProcessor\DefaultApiDataProcessor;
use App\Api\Consumer\DataProvider\HttpApiDataProvider;
use App\Api\Consumer\Model\AbstractDemand;
use App\Entity\Api\ApiConsumer as ApiConsumerEntity;
use App\Import\Annotation\InjectAnnotationReaderTrait;
use App\Import\DataProcessor\DataProcessorInterface;
use App\Import\Model\ResultCollection;
use App\Import\OutputInterfaceTrait;
use App\Util\SnakeCaseConverter;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractApiConsumer implements ApiConsumerInterface, LoggerAwareInterface
{
    use InjectAnnotationReaderTrait;
    use LoggerAwareTrait;
    use OutputInterfaceTrait;

    /**
     * @var DataProcessorInterface|DefaultApiDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var AbstractDemand
     */
    protected $demand;
    /**
     * @var HttpApiDataProvider
     */
    protected $dataProvider;

    /**
     * @var ApiConsumerEntity
     */
    protected $apiConsumerEntity;

    /**
     * @required
     * @param HttpApiDataProvider $dataProvider
     */
    public function injectApiManager(HttpApiDataProvider $dataProvider): void
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Returns the query url
     *
     * @return string
     */
    public function getQueryUrl(): string
    {
        return $this->dataProvider->getQueryUrl();
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getName(): string
    {
        return $this->getApiConsumerEntity()->getName();
    }

    public function getDescription(): string
    {
        return $this->getApiConsumerEntity()->getDescription();
    }

    /**
     * Searches for the submitted demand values and returns the search result
     *
     * @return ResultCollection The result content
     */
    public function search(): ResultCollection
    {
        $this->dataProvider->setDemand($this->getDemand());
        $this->dataProvider->setApiConsumerEntity($this->getApiConsumerEntity());
        $this->dataProcessor->setImportModelClass($this->getImportModelClass());
        $this->dataProcessor->setOutput($this->output);
        $this->dataProcessor->setImportSource($this->getImportSourceKey());
        $this->dataProvider->process($this->dataProcessor);
        //$this->dataProcessor->processImportedRows();
        return $this->dataProcessor->getResultCollection();
    }

    /**
     * Returns the key for the provider instance
     *
     * @return string
     */
    public function getImportSourceKey(): string
    {
        $reflect = new \ReflectionClass($this);
        return SnakeCaseConverter::classNameToSnakeCase(str_replace('Consumer', '', $reflect->getShortName()));
    }

    /**
     * Returns the class name for the demand model
     *
     * @return string
     */
    abstract protected function getDemandClass(): string;

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

    /**
     * Returns the API demand instance
     *
     * @return AbstractDemand
     */
    public function getDemand(): AbstractDemand
    {
        if (null === $this->demand) {
            $demandClass = $this->getDemandClass();
            $this->demand = new $demandClass();
        }
        return $this->demand;
    }

    /**
     * Returns the class name for the demand model
     *
     * @param string|null $query
     */
    public function initializeDemand(?string $query): void
    {
        if ($query) {
            $demandModelConfiguration = $this->getDemandPropertyConfiguration();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $queryParameters = explode('|', $query);
            $offset = 0;
            foreach ($demandModelConfiguration as $property => $configuration) {
                if (!empty($queryParameters[$offset])
                    && $configuration->isSearchProperty()
                    && $propertyAccessor->isWritable($this->demand, $property)) {
                    $value = str_replace('$$', '|', $queryParameters[$offset]);
                    $propertyAccessor->setValue($this->demand, $property, $value);
                }
                ++$offset;
            }
        }
    }

    /**
     * Returns the api demand model configuration
     *
     * @return ApiSearchModelAnnotation[]|array
     */
    public function getDemandPropertyConfiguration(): array
    {
        return $this->annotationReader->getModelPropertyConfiguration($this->getDemandClass());
    }

    /**
     * Returns the value string for the demand query
     *
     * @return string
     */
    public function getDemandValueString(): string
    {
        $values = [];
        $demandModelConfiguration = $this->getDemandPropertyConfiguration();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($demandModelConfiguration as $property => $propertyConfiguration) {
            if ($propertyConfiguration->isSearchProperty()) {
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
}

