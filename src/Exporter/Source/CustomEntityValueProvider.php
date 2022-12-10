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

namespace App\Exporter\Source;

use App\Entity\Base\BaseEntity;
use App\Model\ExportCellValue;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * Class CustomEntityValueProvider
 * Support export of collection fields and use caching for collection fields
 *
 * the current function in Sonata\Exporter\Source\DoctrineORMQuerySourceIterator is marked as final and can therefore
 * not be extended
 * => Override whole file to enable caching in "current" function
 */
class CustomEntityValueProvider
{
    public const CACHE_PREFIX = 'cqsi';

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var array|PropertyPath[]
     */
    protected $propertyPaths = [];

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @var string default DateTime format
     */
    protected $dateTimeFormat;
    /**
     * @var string
     */
    private $context;

    /**
     * Temporary cache for data records
     * Store data in groups of 200 records. Because the cache is updated in the indexing process the entities are
     * all updated at the same time and normally the cache entry will be valid
     * Since the records are only used in the export and the exported items are usually exported by sorted by id,
     * this will speed up the export process
     * Additionally this reduces he number of cache files
     * If sorted by any other field than id, the export will be a little slower
     *
     * @var array
     */
    private $dataGroupCache = [];

    /**
     * @param array $fields Fields to export
     * @param CacheItemPoolInterface $cache
     * @param string $context
     * @param string $dateTimeFormat
     */
    public function __construct(array $fields, CacheItemPoolInterface $cache, string $context, string $dateTimeFormat = 'r')
    {
        foreach ($fields as $name => $field) {
            if (is_string($name) && is_string($field)) {
                $this->propertyPaths[$name] = new PropertyPath($field);
            } else {
                $this->propertyPaths[$field] = new PropertyPath($field);
            }
        }
        $this->dateTimeFormat = $dateTimeFormat;
        $this->cache = $cache;
        $this->context = $context;
        // Add property accessor cache to improve performance
        $propertyAccessorBuilder = PropertyAccess::createPropertyAccessorBuilder();
        $propertyAccessorBuilder->setCacheItemPool($this->cache);
        $this->propertyAccessor = $propertyAccessorBuilder->getPropertyAccessor();
    }

    public function getItemData($objectOrArray)
    {
        if ($objectOrArray instanceof BaseEntity) {
            $data = $this->getCacheItemData($objectOrArray);
            unset($data['_tstamp']);
            return $data;
        }
        return $this->processData($objectOrArray);
    }

    /**
     * Update the cache items for the given entity
     *
     * @param BaseEntity $objectOrArray
     */
    public function updateCacheItemData(BaseEntity $objectOrArray): void
    {
        $this->getCacheItemData($objectOrArray, true);
    }

    /**
     * Returns the property values of the given entity from the cache; sets/updates the cache entry if necessary
     *
     * @param BaseEntity $entity
     * @param bool $forceUpdate Force update of cache item
     * @return array|mixed
     */
    protected function getCacheItemData(BaseEntity $entity, $forceUpdate = false)
    {
        $itemId = $entity->getId();
        if (null !== $modifiedAt = $entity->getModifiedAt()) {
            $tstamp = $modifiedAt->getTimestamp();
            $lastChanged = $tstamp;
        } else {
            $tstamp = time();
            $lastChanged = strtotime('-2 weeks');
        }
        // First check if the given record is in the list of cached records
        if (!$forceUpdate && isset($this->dataGroupCache[$itemId]) &&
            !empty($this->dataGroupCache[$itemId]['_tstamp']) &&
            $this->dataGroupCache[$itemId]['_tstamp'] >= $lastChanged) {
            return $this->dataGroupCache[$itemId];
        }
        $itemGroup = intdiv($itemId, 200);
        $key = str_replace('\\', '.', get_class($entity)) . '.' . $itemGroup;
        try {
            $item = $this->cache->getItem(self::CACHE_PREFIX . $this->context . rawurlencode($key));
            if (!$forceUpdate && $item->isHit()) {
                $data = $item->get();
                $this->dataGroupCache = $data;
                if (!empty($data[$itemId]) && !empty($data[$itemId]['_tstamp']) &&
                    $data[$itemId]['_tstamp'] >= $lastChanged) {
                    return $data[$itemId];
                }
            }
        } catch (InvalidArgumentException $e) {
            $item = null;
            unset($e);
        }
        $data[$itemId] = $this->getPropertyValueModelList($entity);
        $data[$itemId]['_tstamp'] = $tstamp;

        if ($item) {
            $this->cache->save($item->set($data));
            $this->dataGroupCache = $data;
        }
        return $data[$itemId];
    }

    /**
     * @param BaseEntity $entity
     * @return ExportCellValue[]
     */
    protected function getPropertyValueModelList($entity): array
    {
        return $this->processData($entity);
    }

    /**
     * @param object|array $objectOrArray
     * @return array
     */
    protected function processData($objectOrArray): array
    {
        $data = [];
        foreach ($this->propertyPaths as $name => $propertyPath) {
            $data[$name] = $this->getPropertyValue($propertyPath, $objectOrArray);
        }
        return $data;
    }

    /**
     * Returns the property value for the given object or array
     * @param string $propertyPath
     * @param object|array $objectOrArray
     * @return string|null
     */
    protected function getPropertyValue(string $propertyPath, $objectOrArray): ?string
    {
        try {
            $rawValue = $this->propertyAccessor->getValue($objectOrArray, $propertyPath);
            return ExportCellValue::formatValue($rawValue, $this->dateTimeFormat);
        } catch (UnexpectedTypeException $e) {
            //non-existent object in path will be ignored
            return null;
        }
    }
}