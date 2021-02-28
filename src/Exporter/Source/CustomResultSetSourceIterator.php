<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Exporter\Source;

use App\Model\ExportSettings;
use Doctrine\Common\Collections\Collection;
use Psr\Cache\CacheItemPoolInterface;
use Sonata\Exporter\Source\SourceIteratorInterface;

/**
 * Class CustomQuerySourceIterator
 * Support export of collection fields and use caching for collection fields
 *
 * the current function in Sonata\Exporter\Source\DoctrineORMQuerySourceIterator is marked as final and can therefore
 * not be extended
 * => Override whole file to enable caching in "current" function
 */
class CustomResultSetSourceIterator extends CustomEntityValueProvider implements SourceIteratorInterface
{
    /**
     * @var \ArrayIterator|\Traversable
     */
    protected $resultIterator;

    /**
     * @var ExportSettings
     */
    private $exportSettings;

    /**
     * @param Collection $results The custom result set
     * @param CacheItemPoolInterface $cache
     * @param ExportSettings $exportSettings
     */
    public function __construct(
        Collection $results,
        CacheItemPoolInterface $cache,
        ExportSettings $exportSettings
    )
    {
        parent::__construct(
            $exportSettings->getProcessedPropertyMap(),
            $cache,
            $exportSettings->getContext(),
            $exportSettings->getDateTimeFormat()
        );
        $this->resultIterator = $results->getIterator();
        $this->exportSettings = $exportSettings;
    }

    /**
     * Returns the property value for the given object or array
     * @param string $propertyPath
     * @param object|array $objectOrArray
     * @return string|null
     */
    protected function getPropertyValue(string $propertyPath, $objectOrArray): ?string
    {
        if (null !== $customFormatter = $this->exportSettings->getCustomPropertyValueFormatter($propertyPath)) {
            return $customFormatter->getPropertyValue($propertyPath, $objectOrArray);
        }
        return parent::getPropertyValue($propertyPath, $objectOrArray);
    }

    final public function current()
    {
        $current = $this->resultIterator->current();

        $data = $this->getItemData($current);

        return $data;
    }

    final public function next(): void
    {
        $this->resultIterator->next();
    }

    final public function key()
    {
        return $this->resultIterator->key();
    }

    final public function valid(): bool
    {
        return $this->resultIterator->valid();
    }

    final public function rewind(): void
    {
        $this->resultIterator->rewind();
    }
}