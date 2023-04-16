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

use App\Admin\AbstractContextAwareAdmin;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Base\NamedEntityInterface;
use App\Model\ExportCellValue;
use App\Model\ExportSettings;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Query;
use Psr\Cache\CacheItemPoolInterface;
use Sonata\Exporter\Source\DoctrineORMQuerySourceIterator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as RoutingUrlGeneratorInterface;

/**
 * Class CustomQuerySourceIterator
 * Support export of collection fields and use caching for collection fields
 *
 * the current function in Sonata\Exporter\Source\DoctrineORMQuerySourceIterator is marked as final and can therefore
 * not be extended
 * => Override whole file to enable caching in "current" function
 * @see DoctrineORMQuerySourceIterator
 */
final class CustomQuerySourceIterator extends CustomEntityValueProvider implements \Iterator
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
     * @var AbstractContextAwareAdmin
     */
    private $admin;

    protected $mapUrlProperties = [];
    private int $batchSize;

    /**
     * @param AbstractContextAwareAdmin $admin
     * @param Query $query The Doctrine Query
     * @param CacheItemPoolInterface $cache
     * @param ExportSettings $exportSettings
     * @param int $batchSize
     */
    public function __construct(
        AbstractContextAwareAdmin $admin,
        Query                     $query,
        CacheItemPoolInterface    $cache,
        ExportSettings            $exportSettings,
        int                       $batchSize = 100
    )
    {
        parent::__construct(
            $exportSettings->getProcessedPropertyMap(),
            $cache,
            $exportSettings->getContext(),
            $exportSettings->getDateTimeFormat()
        );
        $this->query = clone $query;
        $this->query->setParameters($query->getParameters());
        foreach ($query->getHints() as $name => $value) {
            $this->query->setHint($name, $value);
        }
        $this->exportSettings = $exportSettings;
        $this->admin = $admin;
        if (is_a($admin->getClass(), NamedEntityInterface::class, true)) {
            $this->mapUrlProperties['name'] = 'show';
        }
        $this->batchSize = $batchSize;
    }

    /**
     * @param BaseEntityInterface $entity
     * @return ExportCellValue[]
     */
    protected function getPropertyValueModelList(BaseEntityInterface $entity): array
    {
        $data = [];
        foreach ($this->propertyPaths as $name => $propertyPath) {
            $value = $this->getPropertyValue($propertyPath, $entity);
            $propertyName = $propertyPath . '';
            if (!empty($this->mapUrlProperties[$propertyName])) {
                $valueModel = new ExportCellValue($name, $value);
                $url = $this->admin->generateContextObjectUrl(
                    $this->mapUrlProperties[$propertyName],
                    $entity,
                    [],
                    RoutingUrlGeneratorInterface::ABSOLUTE_URL
                );
                $valueModel->setUrl($url);
                $data[$name] = $valueModel;
            } else {
                $data[$name] = $value;
            }
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
        if (null !== $customFormatter = $this->exportSettings->getCustomPropertyValueFormatter($propertyPath)) {
            return $customFormatter->getPropertyValue($propertyPath, $objectOrArray);
        }
        return parent::getPropertyValue($propertyPath, $objectOrArray);
    }

    public function current()
    {
        $current = $this->iterator->current();
;
        $data = $this->getItemData($current);

        //$this->query->getEntityManager()->getUnitOfWork()->detach($current);

        if (0 === ($this->getIterator()->key() % $this->batchSize)) {
            $this->query->getEntityManager()->clear();
        }

        return $data;
    }

    public function next(): void
    {
        $this->getIterator()->next();
    }


    public function key(): mixed
    {
        return $this->getIterator()->key();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator = $this->iterableToIterator($this->query->toIterable());
        $this->iterator->rewind();
    }

    /**
     * @param iterable<mixed> $iterable
     */
    private function iterableToIterator(iterable $iterable): \Iterator
    {
        if ($iterable instanceof \Iterator) {
            return $iterable;
        }
        if (\is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }

        return new \ArrayIterator(iterator_to_array($iterable));
    }

    protected function getIterator(): \Iterator
    {
        if (null === $this->iterator) {
            throw new \LogicException('The iterator MUST be set in the "rewind()" method.');
        }

        return $this->iterator;
    }
}