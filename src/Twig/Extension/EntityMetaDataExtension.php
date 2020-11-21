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

namespace App\Twig\Extension;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\MetaData\MetaItem;
use App\Entity\MetaData\MetaItemProperty;
use App\Util\SnakeCaseConverter;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EntityMetaDataExtension extends AbstractExtension
{
    use InjectManagerRegistryTrait;

    /**
     * @var array|MetaItem[]
     */
    protected $metaCache = [];

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_entity_class_meta_data', [$this, 'getObjectClassMetaData']),
            new TwigFunction('app_object_property_meta_data', [$this, 'getObjectClassMetaProperty']),
        ];
    }

    /**
     * Returns the meta data for the entity class managed by this admin; returns null if entity has no meta data
     * @param object|string $objectOrClass
     * @return MetaItem|null
     */
    public function getObjectClassMetaData($objectOrClass): ?MetaItem
    {
        $objectClassName = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;
        $metaKey = SnakeCaseConverter::classNameToSnakeCase($objectClassName);
        if (!array_key_exists($metaKey, $this->metaCache)) {
            $metaItem = null;
            if (is_a($objectClassName, HasMetaDateEntityInterface::class, true)) {
                $em = $this->getEntityManager();
                /** @var ModelManager $modelManager */
                $repository = $em->getRepository(MetaItem::class);
                $metaItem = $repository->findOneBy(['metaKey' => $metaKey]);
                /** @var MetaItem|null $metaItem */
            }
            $this->metaCache[$metaKey] = $metaItem;
        }
        return $this->metaCache[$metaKey];
    }

    /**
     * Returns the meta item property entity for the given object (or object class name) and property key.
     * Returns null if no meta data for the class or property exist
     *
     * @param object|string $objectOrClass The entity class name
     * @param string $propertyKey The object property name
     * @return MetaItem|null
     */
    public function getObjectClassMetaProperty($objectOrClass, string $propertyKey): ?MetaItemProperty
    {
        if (null !== $metaItem = $this->getObjectClassMetaData($objectOrClass)) {
            return $metaItem->getMetaItemProperty($propertyKey);
        }
        return null;
    }
}
