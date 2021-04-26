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

use App\Entity\MetaData\MetaItem;
use App\Entity\MetaData\MetaItemProperty;
use App\Service\MetaData\InjectMetaDataManagerTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EntityMetaDataExtension extends AbstractExtension
{
    use InjectMetaDataManagerTrait;

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
            new TwigFunction('app_entity_class_meta_data_array', [$this, 'getObjectClassMetaDataAsArray']),
        ];
    }

    /**
     * Returns the meta data for the entity class managed by this admin as a json string;
     * returns null if entity has no meta data
     *
     * @param object|string $objectOrClass
     * @return array|null
     */
    public function getObjectClassMetaDataAsArray($objectOrClass): ?array
    {
        if (null !== $metaItem = $this->getObjectClassMetaData($objectOrClass)) {
            $data = [];
            foreach ($metaItem->getMetaItemProperties() as $metaItemProperty) {
                $customLabel = $metaItemProperty->getCustomLabel();
                $description = $metaItemProperty->getDescription();
                if ($customLabel || $description) {
                    $propertyName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $metaItemProperty->getMetaKey()))));
                    $data[] = [
                        'key' => $metaItemProperty->getMetaKey(),
                        'property' => $propertyName,
                        'customLabel' => $customLabel,
                        'description' => $description,
                    ];
                }
            }
            return empty($data) ? null : $data;
        }
        return null;
    }

    /**
     * Returns the meta data for the entity class managed by this admin; returns null if entity has no meta data
     * @param object|string $objectOrClass
     * @return MetaItem|null
     */
    public function getObjectClassMetaData($objectOrClass): ?MetaItem
    {
        return $this->metaDataManager->getObjectClassMetaData($objectOrClass);
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
