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

namespace App\Model;

use App\Entity\Base\BaseEntityInterface;

/**
 * Class EntityReferenceMap
 * Process entity references to find problems when deleting an entity
 *
 * @package App\Model
 */
class EntityReferenceMap
{
    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var EntityReferenceProperty[]
     */
    private $propertyReferences = [];

    /**
     * Contains all object references that cannot be modified
     *
     * @var array
     */
    protected $errorObjectList = [];

    /**
     * Prevent infinite loops of object initialization
     *
     * @var array
     */
    private $toggleInitStatus = [];

    /**
     * EntityReferenceProperty constructor.
     * @param string $entityClass
     */
    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $property
     * @param EntityReferenceProperty $propertyReference
     */
    public function addPropertyReferences(string $property, EntityReferenceProperty $propertyReference): void
    {
        $this->propertyReferences[$property] = $propertyReference;
    }

    /**
     * @param string $action
     * @return int
     */
    public function getWarnLevel(string $action = EntityReferenceProperty::ACTION_DELETE): int
    {
        $warnLevel = EntityReferenceProperty::LEVEL_OK;
        $referenceProperties = $this->getPropertyReferences();
        foreach ($referenceProperties as $meta) {
            if ($meta->getWarnLevel($action) > $warnLevel) {
                $warnLevel = $meta->getWarnLevel($action);
            }
        }

        return $warnLevel;
    }

    /**
     * Returns the inverse property reference of the given reference
     *
     * @param EntityReferenceProperty $reference The property reference
     *
     * @return EntityReferenceProperty|null
     */
    public function getInversePropertyReference(EntityReferenceProperty $reference): ?EntityReferenceProperty
    {
        $name = $reference->getName();
        $refMapping = $reference->getMapping();
        $targetEntity = $refMapping['targetEntity'];
        if ($refMapping['fieldName'] !== $name) {
            $targetProperty = $refMapping['fieldName'];
        } else {
            $targetPropertyKey = $refMapping['isOwningSide'] ? 'inversedBy' : 'mappedBy';
            $targetProperty = $refMapping[$targetPropertyKey];
        }
        $inverseReference = null;
        $referenceProperties = $this->getPropertyReferences();
        foreach ($referenceProperties as $meta) {
            $mapping = $meta->getMapping();
            if ($targetEntity === $this->entityClass && $mapping['fieldName'] === $targetProperty) {
                $inverseReference = $meta;
                break;
            }
        }
        return $inverseReference;
    }

    /**
     * @param string $name The name of the referenced property
     *
     * @return EntityReferenceProperty|null
     */
    public function getPropertyReference(string $name): ?EntityReferenceProperty
    {
        $reference = null;
        $referenceProperties = $this->getPropertyReferences();
        foreach ($referenceProperties as $meta) {
            $mapping = $meta->getMapping();
            if ($mapping['sourceEntity'] === $this->entityClass) {
                $mapField = $mapping['isOwningSide'] ? 'inversedBy' : 'fieldName';
                if ($mapping[$mapField] === $name) {
                    $reference = $meta;
                    break;
                }
            } else {
                if ($mapping['fieldName'] === $name) {
                    $reference = $meta;
                    break;
                }
            }
        }
        return $reference;
    }

    /**
     * @return EntityReferenceProperty[]
     */
    public function getPropertyReferences(): array
    {
        return $this->propertyReferences;
    }

    /**
     * Returns true, if this entity class has references to other entities
     *
     * @return bool
     */
    public function hasReferences(): bool
    {
        return count($this->propertyReferences) > 0;
    }

    /**
     * @param EntityReferenceProperty[] $propertyReferences
     */
    public function setPropertyReferences(array $propertyReferences): void
    {
        $this->propertyReferences = $propertyReferences;
    }

    /**
     * Returns true if any property of this reference map has any errors
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->errorObjectList) > 0;
    }

    /**
     * @param BaseEntityInterface|object $object
     * @param string $parentAction
     * @return array $errorObjectList
     */
    public function initObjectActions($object, string $parentAction): array
    {
        $this->errorObjectList = [];
        $referenceProperties = $this->getPropertyReferences();
        foreach ($referenceProperties as $meta) {
            $key = $this->getEntityClass() . '::' . $meta->getName();
            if (empty($this->toggleInitStatus[$key])) {
                $this->toggleInitStatus[$key] = true;
                if ($meta->isMappedOneSided()) {
                    $metaAction = $meta->getPerformedAction($parentAction);
                    if ($metaAction === EntityReferenceProperty::ACTION_ERROR) {
                        $objectKey = get_class($object) . '::' . $object->getId();
                        $this->errorObjectList[$objectKey] = $metaAction;
                    }
                    if (null !== $meta->getTargetEntityReferenceMap()) {
                        $childReferenceProperties = $this->getPropertyReferences();
                        foreach ($childReferenceProperties as $childMeta) {
                            $childAction = $childMeta->getPerformedAction($metaAction);
                            if ($childAction === EntityReferenceProperty::ACTION_ERROR) {
                                $objectKey = get_class($childMeta);
                                $this->errorObjectList[$objectKey] = $childAction;
                            }
                        }
                    }
                } else {
                    $values = $meta->getObjectIterableValue($object);
                    foreach ($values as $value) {
                        $objectAction = $meta->getPerformedObjectAction($object, $parentAction);
                        if ($objectAction === EntityReferenceProperty::ACTION_ERROR) {
                            $objectKey = get_class($object) . '::' . $object->getId();
                            $this->errorObjectList[$objectKey] = $objectAction;
                        }
                        if ($value !== $object && EntityReferenceProperty::actionRequiresCheck($objectAction)) {
                            if (null !== $entityMap = $meta->getTargetEntityReferenceMap()) {
                                $mapErrorObjectList = $entityMap->initObjectActions($value, $objectAction);
                                if (!empty($mapErrorObjectList)) {
                                    $this->errorObjectList = array_merge($mapErrorObjectList, $mapErrorObjectList);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $this->errorObjectList;
    }

}
