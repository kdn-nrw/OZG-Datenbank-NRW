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

use App\Model\ExportSettings;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Query;
use Sonata\Exporter\Exception\InvalidMethodCallException;
use Sonata\Exporter\Source\SourceIteratorInterface;

/**
 * Class CustomQuerySourceIterator
 * Support export of collection fields and use caching for collection fields
 *
 * the current function in Sonata\Exporter\Source\DoctrineORMQuerySourceIterator is marked as final and can therefore
 * not be extended
 * => Override whole file to enable caching in "current" function
 */
class CustomQuerySourceIterator extends CustomEntityValueProvider implements SourceIteratorInterface
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var IterableResult
     */
    protected $iterator;
    /**
     * @var ExportSettings
     */
    private $exportSettings;

    /**
     * @param Query $query The Doctrine Query
     * @param string|null $cacheDir
     * @param ExportSettings $exportSettings
     */
    public function __construct(
        Query $query,
        ?string $cacheDir,
        ExportSettings $exportSettings
    )
    {
        parent::__construct(
            $exportSettings->getProcessedPropertyMap(),
            $cacheDir,
            $exportSettings->getContext(),
            $exportSettings->getDateTimeFormat()
        );
        $this->query = clone $query;
        $this->query->setParameters($query->getParameters());
        foreach ($query->getHints() as $name => $value) {
            $this->query->setHint($name, $value);
        }
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
        $current = $this->iterator->current();

        $data = $this->getItemData($current[0]);

        $this->query->getEntityManager()->getUnitOfWork()->detach($current[0]);

        return $data;
    }

    final public function next(): void
    {
        $this->iterator->next();
    }

    final public function key()
    {
        return $this->iterator->key();
    }

    final public function valid(): bool
    {
        return $this->iterator->valid();
    }

    final public function rewind(): void
    {
        if ($this->iterator) {
            throw new InvalidMethodCallException('Cannot rewind a Doctrine\ORM\Query');
        }

        $this->iterator = $this->query->iterate();
        $this->iterator->rewind();
    }
}