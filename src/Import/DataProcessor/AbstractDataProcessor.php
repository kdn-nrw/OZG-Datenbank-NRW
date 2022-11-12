<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Import\DataProcessor;

use App\Api\Consumer\Model\AbstractResult;
use App\Api\Consumer\Model\ZuFi\ZuFiResultCollection;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Base\BaseEntity;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\ImportEntityInterface;
use App\Entity\Organisation;
use App\Entity\OrganisationEntityInterface;
use App\Import\Annotation\ImportModelAnnotation;
use App\Import\Annotation\InjectAnnotationReaderTrait;
use App\Import\DataParser;
use App\Import\Exception\GeneralImportException;
use App\Import\Model\AbstractImportModel;
use App\Import\Model\PropertyMappingInterface;
use App\Import\Model\ResultCollection;
use App\Import\OutputInterfaceTrait;
use App\Model\Annotation\BaseModelAnnotation;
use App\Translator\TranslatorAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use ReflectionException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractDataProcessor implements DataProcessorInterface, LoggerAwareInterface
{
    //private const DATA_TYPE_CHOICE = 'choice';

    use LoggerAwareTrait;
    use OutputInterfaceTrait;
    use TranslatorAwareTrait;
    use InjectManagerRegistryTrait;
    use InjectAnnotationReaderTrait;

    /**
     * @var callable[]|array
     */
    protected $callbacks = [];

    /**
     * Cache import meta information, e.g. translated choice labels
     * @var array
     */
    protected $importMetaCache = [];

    /**
     * The class name of the import model
     *
     * @var string
     */
    protected $importModelClass;

    /**
     * The class name of the import model
     *
     * @var string
     */
    protected $importSource;

    /**
     * @var ResultCollection
     */
    protected $resultCollection;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    /**
     * Initialize the result collection
     * @return ResultCollection
     */
    public function getResultCollection(): ResultCollection
    {
        if (null === $this->resultCollection) {
            $this->resultCollection = new ResultCollection();
        }
        return $this->resultCollection;
    }

    /**
     * Unset the result collection
     */
    public function unsetResultCollection(): void
    {
        $this->resultCollection = null;
    }

    /**
     * Set field map for all properties in result collection (used for custom labels)
     *
     * @param ResultCollection $resultCollection
     * @param string $modelClass
     * @param string|null $parentProperty
     * @throws ReflectionException
     */
    protected function setPropertyFieldMap(ResultCollection $resultCollection, string $modelClass, string $parentProperty = null): void
    {
        $modelConfiguration = $this->annotationReader->getModelPropertyConfiguration($modelClass);

        foreach ($modelConfiguration as $property => $propertyConfiguration) {
            $propertyPath = $parentProperty ? $parentProperty . '.' . $property : $property;
            $resultCollection->addPropertyMapping($propertyPath, $propertyConfiguration->getParameter());
            $dataType = $propertyConfiguration->getDataType();
            if (($dataType === BaseModelAnnotation::DATA_TYPE_MODEL
                    || $dataType === BaseModelAnnotation::DATA_TYPE_COLLECTION)
                && $refModelClass = $propertyConfiguration->getModelClass()) {
                $this->setPropertyFieldMap($resultCollection, $refModelClass, $property);
            }
        }
    }

    /**
     * @param string $importModelClass
     */
    public function setImportModelClass(string $importModelClass): void
    {
        $this->importModelClass = $importModelClass;
    }

    /**
     * @return string
     */
    protected function getImportModelClass(): string
    {
        return $this->importModelClass;
    }

    /**
     * @param string $importSource
     */
    public function setImportSource(string $importSource): void
    {
        $this->importSource = $importSource;
    }

    /**
     * Adds a callback for the given property
     *
     * @param string $property The property name
     * @param callable $callback The callback function
     */
    public function addCallback(string $property, callable $callback): void
    {
        $this->callbacks[$property] = $callback;
    }

    /**
     * Executes the property callback
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    public function runCallback(string $property, $value)
    {
        $callback = $this->callbacks[$property] ?? null;
        if (is_callable($callback)) {
            return $callback($value);
        }
        return $value;
    }

    /**
     * Fill in data for DB queries and updates/inserts.
     * PDO::prepare will escape parameters automatically later.
     *
     * @param array $row
     * @param int $rowNr
     *
     * @return void
     * @throws ReflectionException
     */
    public function addRecordRaw(array $row, int $rowNr): void
    {
        $modelClass = $this->getImportModelClass();
        $importConfiguration = $this->annotationReader->getModelPropertyConfiguration($modelClass);
        /** @var AbstractImportModel $importModel */
        $importModel = new $modelClass();
        $this->mapRowToModel($importConfiguration, $importModel, $row, $rowNr);
        $this->getResultCollection()->add($importModel);
    }

    /**
     * Add custom base data to result collection
     *
     * @param array $data
     * @throws ReflectionException
     */
    public function addBaseResultData(array $data): void
    {
        $results = $this->getResultCollection();
        $collectionConfiguration = $this->annotationReader->getModelPropertyConfiguration(get_class($results));
        $this->mapRowToModel($collectionConfiguration, $results, $data, 1);
        $this->setPropertyFieldMap($results, get_class($results));
    }

    /**
     * Map the given api result row to the result model
     *
     * @param ImportModelAnnotation[]|array $modelConfiguration The result model property mapping configuration
     * @param PropertyMappingInterface $importModel The target model instance
     * @param array $row The api result row
     * @param int $rowNr
     * @return bool Returns true if row is valid; false otherwise
     * @throws ReflectionException
     */
    protected function mapRowToModel(array $modelConfiguration, PropertyMappingInterface $importModel, array $row, int $rowNr): bool
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $unmappedData = $row;
        $parser = new DataParser();
        $mapKeys = [];
        foreach (array_keys($row) as $key) {
            $mapKeys[$parser->getCleanFieldName($key)] = $key;
        }
        //$importModel->setRawData($row);
        foreach ($modelConfiguration as $propertyName => $propertyConfiguration) {
            /** @var ImportModelAnnotation $propertyConfiguration */
            $parameter = $parser->getCleanFieldName($propertyConfiguration->getParameter());
            $importFieldName = $mapKeys[$parameter] ?? null;
            $isRequired = $propertyConfiguration->isRequired();
            $processedValue = null;
            $tmpVal = $row[$importFieldName] ?? null;
            if ($isRequired && (empty($tmpVal) || (is_scalar($tmpVal) && (string)$row[$importFieldName] === ''))) {
                $isAutoIncrement = $propertyConfiguration->isAutoIncrement();
                if ($isAutoIncrement) {
                    $row[$importFieldName] = $rowNr;
                } else {
                    $unmappedData['_key_' . $importFieldName] = -1;
                    $importModel->setUnmappedData($unmappedData);
                    return false;
                }
            }
            if ($importFieldName && array_key_exists($importFieldName, $row)) {
                $fieldDataType = $propertyConfiguration->getDataType();
                switch ($fieldDataType) {
                    case BaseModelAnnotation::DATA_TYPE_CALLBACK:
                        $processedValue = $this->runCallback($propertyName, $row[$importFieldName]);
                        break;
                    /*case BaseModelAnnotation::DATA_TYPE_CHOICE:
                        if (!isset($this->importMetaCache[$propertyName]['_translated_choices'])) {
                            $choices = [];
                            foreach ($fieldData['choices'] as $choiceKey => $choiceLabel) {
                                $choices[$choiceKey] = $this->translate($choiceLabel);
                            }
                            $this->importMetaCache[$propertyName]['_translated_choices'] = $choices;
                        } else {
                            $choices = $this->importMetaCache[$propertyName]['_translated_choices'];
                        }
                        $processedValue = $parser->formatChoice($row[$importFieldName], $choices);
                        break;*/
                    case BaseModelAnnotation::DATA_TYPE_MODEL:
                        if (!empty($row[$importFieldName])) {
                            $refModelClass = $propertyConfiguration->getModelClass();
                            if ($refModelClass) {
                                /** @var PropertyMappingInterface $refModel */
                                $processedValue = new $refModelClass();
                                $this->mapRowToModel(
                                    $this->annotationReader->getModelPropertyConfiguration($refModelClass),
                                    $processedValue,
                                    $row[$importFieldName],
                                    1
                                );
                            } elseif ($targetEntity = $propertyConfiguration->getTargetEntity()) {
                                $mapToProperty = $propertyConfiguration->getMapToProperty() ?? 'name';
                                $processedValue = $this->findOrCreateTargetEntity($row[$importFieldName], $targetEntity, $mapToProperty, $fieldDataType);
                            }
                        }
                        break;
                    case BaseModelAnnotation::DATA_TYPE_COLLECTION:
                        $processedValue = $propertyAccessor->getValue($importModel, $propertyName);
                        if (null === $processedValue) {
                            $processedValue = new ResultCollection();
                        }
                        if (!empty($row[$importFieldName])) {
                            $refModelClass = $propertyConfiguration->getModelClass();
                            if ($refModelClass) {
                                /** @var ResultCollection $processedValue */
                                $refPropertyConfiguration = $this->annotationReader->getModelPropertyConfiguration($refModelClass);
                                $subRowNr = 0;
                                foreach ($row[$importFieldName] as $refRow) {
                                    ++$subRowNr;
                                    /** @var AbstractResult $refModel */
                                    $refModel = new $refModelClass();
                                    $this->mapRowToModel(
                                        $refPropertyConfiguration,
                                        $refModel,
                                        $refRow,
                                        $subRowNr
                                    );
                                    $processedValue->add($refModel);
                                }
                                $processedValue->setTotalResultCount(count($processedValue));
                            } elseif ($targetEntity = $propertyConfiguration->getTargetEntity()) {
                                $mapToProperty = $propertyConfiguration->getMapToProperty() ?? 'name';
                                $processedValue = $this->findOrCreateTargetEntity($row[$importFieldName], $targetEntity, $mapToProperty, $fieldDataType);
                            }
                        }
                        break;
                    case BaseModelAnnotation::DATA_TYPE_ARRAY:
                        $tmpVal = $row[$importFieldName];
                        $mapValues = is_iterable($tmpVal) ? $tmpVal : explode(',', $tmpVal);
                        $processedValue = $mapValues;
                        break;
                    default:
                        $processedValue = $row[$importFieldName];
                        if (is_array($processedValue)) {
                            $processedValue = current($processedValue);
                        }
                        $ccKey = ucwords(str_replace('_', ' ', $fieldDataType));
                        $formatFunction = 'format' . str_replace(' ', '', $ccKey);
                        if (method_exists($parser, $formatFunction)) {
                            $processedValue = $parser->$formatFunction($processedValue);
                        }
                        break;
                }
            }
            // Property must be writable! if ($propertyAccessor->isWritable($importModel, $propertyName)) {}
            $propertyAccessor->setValue($importModel, $propertyName, $processedValue);
            unset($unmappedData[$importFieldName]);
        }
        $importModel->setUnmappedData($unmappedData);
        return true;
    }

    /**
     * Process content of the loaded import rows
     */
    public function processImportedRows(): void
    {
        $resultCollection = $this->getResultCollection();
        $this->setPropertyFieldMap($resultCollection, get_class($resultCollection));
        $this->setPropertyFieldMap($resultCollection, $this->getImportModelClass());
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $rowOffset = 0;
        $accessor = PropertyAccess::createPropertyAccessor();
        $modelEntityPropertyMapping = $this->getModelEntityPropertyMapping();
        foreach ($resultCollection as $importModel) {
            foreach ($modelEntityPropertyMapping as $entityClass => $entityPropertyMapping) {
                $this->processEntity($importModel, $entityClass, $entityPropertyMapping, $accessor);
            }
            ++$rowOffset;
            if ($rowOffset % 100 === 0) {
                $em->flush();
            }
        }
        $em->flush();
    }

    /**
     * Finds or creates the entity of the given entity class that matches the given value
     *
     * @param mixed $value
     * @param string $entityClass
     * @param string $mapValueToProperty
     * @param string $dataType
     * @return ArrayCollection|BaseEntityInterface|null
     */
    protected function findOrCreateTargetEntity(
        $value,
        string $entityClass,
        string $mapValueToProperty = 'name',
        string $dataType = BaseModelAnnotation::DATA_TYPE_MODEL
    )
    {
        if (empty($value)) {
            return null;
        }
        $em = $this->getEntityManager();
        /** @var EntityRepository $repository */
        $repository = $em->getRepository($entityClass);
        if ($dataType === BaseModelAnnotation::DATA_TYPE_COLLECTION) {
            $mapValues = is_iterable($value) ? $value : explode(',', trim(strip_tags($value)));
            $collection = new ResultCollection();
            foreach ($mapValues as $listValue) {
                $listEntity = $this->findOrCreateTargetEntity(
                    $listValue,
                    $entityClass,
                    $mapValueToProperty
                );
                if (null !== $listEntity) {
                    $collection->add($listEntity);
                }
            }
            return $collection;
        }
        $compareValue = trim(strip_tags($value));
        $hasChanges = false;
        $entity = $repository->findOneBy([$mapValueToProperty => $compareValue]);
        if (null === $entity) {
            $entity = new $entityClass();
            $setter = 'set' . ucfirst($mapValueToProperty);
            $entity->$setter($compareValue);
            $em->persist($entity);
            $hasChanges = true;
        }
        if ($entity instanceof OrganisationEntityInterface) {
            $organisation = $entity->getOrganisation();
            if (null === $organisation) {
                $organisation = new Organisation();
                $entity->setOrganisation($organisation);
                $organisation->setName($compareValue);
                $hasChanges = true;
            } elseif (empty($organisation->getName())) {
                $organisation->setName($compareValue);
                $hasChanges = true;
            }
        }
        if ($hasChanges) {
            $em->flush();
        }
        return $entity;
    }

    /**
     * Loads a previously imported entity from the database
     * @param string $entityClass
     * @param AbstractImportModel $importModel
     * @return BaseEntity
     * @throws GeneralImportException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    protected function findOrCreateImportedEntity(string $entityClass, AbstractImportModel $importModel): BaseEntity
    {
        $importKeyData = $importModel->getImportKeyData();
        $em = $this->getEntityManager();
        $expressionBuilder = $em->getExpressionBuilder();
        $targetEntity = null;
        if ($expressionBuilder && is_a($entityClass, ImportEntityInterface::class, true)
            && !empty($importKeyData['importId'])) {
            $importId = (int)$importKeyData['importId'];
            $targetEntity = $this->findEntityByConditions($entityClass, [
                //$expressionBuilder->eq('LOWER(e.name)', ':name'),
                $expressionBuilder->eq('e.importSource', ':importSource'),
                $expressionBuilder->eq('e.importId', ':importId'),
            ], [
                    //'name' => $solutionProperties['name'],
                    'importSource' => $this->importSource,
                    'importId' => $importKeyData['importId'],
                ]
            );
            if (null === $targetEntity) {
                /** @var ImportEntityInterface $targetEntity */
                $targetEntity = new $entityClass();
                $targetEntity->setImportSource($this->importSource);
                $targetEntity->setImportId($importId);
            }
        }
        if (null === $targetEntity) {
            throw new GeneralImportException(sprintf('The import query for the entity class %s is not configured!', $entityClass));
        }
        return $targetEntity;
    }

    /**
     * Either find an existing entity by the given field or create a new entity
     * @param string $entityClass
     * @param array|Criteria $expressions
     * @param array $parameters
     * @return BaseEntity|null
     * @throws \Doctrine\ORM\Query\QueryException
     */
    protected function findEntityByConditions(string $entityClass, $expressions, array $parameters = []): ?BaseEntity
    {
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository($entityClass);
        $qb = $repository->createQueryBuilder('e')
            ->orderBy('e.id', 'ASC');
        if ($expressions instanceof Criteria) {
            $qb->addCriteria($expressions);
        } else {
            $andX = $qb->expr()->andX();
            foreach ($expressions as $expr) {
                $andX->add($expr);
            }
            $qb->where($andX);
        }
        if (!empty($parameters)) {
            $qb->setParameters($parameters);
        }
        $qb->setMaxResults(1);
        /** @var BaseEntity|null $entity */
        try {
            $entity = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $entity = null;
        }
        return $entity;
    }


    /**
     * Process the given import model: find or create the entity and set the model properties in the entity instance
     *
     * @param AbstractImportModel $importModel
     * @param string $entityClass
     * @param array $modelEntityPropertyMapping
     * @param PropertyAccessor $accessor
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    protected function processEntity(
        AbstractImportModel $importModel,
        string              $entityClass,
        array               $modelEntityPropertyMapping,
        PropertyAccessor    $accessor
    ): BaseEntity
    {
        $em = $this->getEntityManager();
        $targetEntity = $this->findOrCreateImportedEntity($entityClass, $importModel);
        if (!$em->contains($targetEntity)) {
            $this->debug('No entity found for name: ' . $importModel);
            $em->persist($targetEntity);
        } else {
            $this->debug('Found entity: ' . $importModel . ' <=> ' . $targetEntity . ' [' . $targetEntity->getId() . ']');
            if (method_exists($targetEntity, 'setHidden')) {
                $targetEntity->setHidden(false);
            }
        }
        foreach ($modelEntityPropertyMapping as $entityProperty => $modelProperty) {
            if ($accessor->isWritable($targetEntity, $entityProperty)) {
                $value = $accessor->getValue($importModel, $modelProperty);
                $accessor->setValue($targetEntity, $entityProperty, $value);
            }
        }
        if ($targetEntity instanceof OrganisationEntityInterface) {
            $organisation = $targetEntity->getOrganisation();
            foreach ($modelEntityPropertyMapping as $entityProperty => $modelProperty) {
                if ($accessor->isWritable($organisation, $entityProperty)) {
                    $value = $accessor->getValue($importModel, $modelProperty);
                    $accessor->setValue($organisation, $entityProperty, $value);
                }
            }
            if (!$em->contains($organisation)) {
                $em->persist($organisation);
            }
        }
        return $targetEntity;
    }

    /**
     * Creates the mapping for the current model properties to the entity class and entity properties
     *
     * @return array
     */
    protected function getModelEntityPropertyMapping(): array
    {
        $modelClass = $this->getImportModelClass();
        return $this->getEntityPropertyMappingForModel($modelClass);
    }

    /**
     * Creates the mapping for the current model properties to the entity class and entity properties
     *
     * @param string $modelClass
     * @return array
     * @throws ReflectionException
     */
    protected function getEntityPropertyMappingForModel(string $modelClass): array
    {
        $importConfiguration = $this->annotationReader->getModelPropertyConfiguration($modelClass);
        $mapping = [];
        foreach ($importConfiguration as $propertyName => $propertyConfiguration) {
            if (!$propertyConfiguration->isDisableImport()) {
                $mapToEntity = $propertyConfiguration->getTargetEntity();
                $mapping[$mapToEntity][$propertyName] = $propertyName;
            }
        }
        return $mapping;
    }

    /**
     * Convert the given collection to an array
     *
     * @param ResultCollection $collection
     * @return array
     * @throws ReflectionException
     */
    protected function convertCollectionToArray(ResultCollection $collection): array
    {
        $dataRows = [];
        $mapProperties = null;
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($collection as $model) {
            $row = [];
            if (null === $mapProperties) {
                $modelPropertyMapping = $this->annotationReader->getModelPropertyConfiguration(get_class($model));
                $mapProperties = array_keys($modelPropertyMapping);
            }
            foreach ($mapProperties as $mapProperty) {
                $value = $accessor->getValue($model, $mapProperty);
                if ($value instanceof ResultCollection) {
                    $row[$mapProperty] = $this->convertCollectionToArray($value);
                } else {
                    $row[$mapProperty] = $value;
                }
            }
            $dataRows[] = $row;
        }
        return $dataRows;
    }
}
