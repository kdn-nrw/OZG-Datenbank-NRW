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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class EntityReferenceProperty
 * Process an entity reference property to find problems when deleting an entity
 *
 * @package App\Model
 */
class EntityReferenceProperty
{
    public const LEVEL_OK = 100;
    public const LEVEL_WARNING = 300;
    public const LEVEL_DANGER = 700;

    public const ON_DELETE_SET_NULL = 'SET NULL';
    public const ON_DELETE_CASCADE = 'CASCADE';


    public const ACTION_DELETE = 'delete';
    public const ACTION_SET_NULL = 'set_null';
    public const ACTION_REMOVE_MM_REFERENCE = 'delete_mm';
    public const ACTION_ERROR = 'error';
    public const ACTION_NOTHING = 'nothing';
    public const ACTION_UNKNOWN = 'unknown';
    public const ACTION_CHECK_OWNER = 'check_owner';
    public const ACTION_CHECK_CHILD_REFERENCES = 'check_children';

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $mapping;

    /**
     * @var EntityReferenceMap
     */
    private $targetEntityReferenceMap;

    /**
     * @var FieldDescriptionInterface|null
     */
    private $fieldDescription;

    /**
     * @var string
     */
    private $actionPerformed;

    /**
     * Indicates if this property reference only exists in another entity and not in the current entity
     * (inversedBy and mappedBy are not set)
     * @var bool
     */
    private $isMappedOneSided = false;

    /**
     * Contains the actions required for all checked objects of this property
     *
     * @var array
     */
    private $objectActions = [];

    /**
     * EntityReferenceProperty constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    /**
     * @param array $mapping
     */
    public function setMapping(array $mapping): void
    {
        $this->mapping = $mapping;
    }

    /**
     * @return FieldDescriptionInterface|null
     */
    public function getFieldDescription(): ?FieldDescriptionInterface
    {
        return $this->fieldDescription;
    }

    /**
     * @param FieldDescriptionInterface $fieldDescription
     */
    public function setFieldDescription(FieldDescriptionInterface $fieldDescription): void
    {
        $this->fieldDescription = $fieldDescription;
    }

    /**
     * Returns the action that will be performed if the reference record will be deleted
     *
     * @param string|null $parentAction
     * @return string
     */
    public function getActionBasedOnMapping(?string $parentAction): string
    {
        if (null !== $parentAction && !self::actionRequiresCheck($parentAction)) {
            return self::ACTION_NOTHING;
        }
        $type = (int)$this->mapping['type'];
        if ($this->isCascadeRemove() || $this->isOnDeleteEqualsValue($this->mapping, self::ON_DELETE_CASCADE)) {
            if ($type === ClassMetadataInfo::MANY_TO_MANY) {
                $action = self::ACTION_REMOVE_MM_REFERENCE;
            } else {
                $action = self::ACTION_DELETE;
            }
        } elseif ($this->isSetNull()) {
            $action = self::ACTION_SET_NULL;
        } elseif ($this->isOwning()) {
            // Many-to-One references are automatically deleted when the parent record is deleted
            // (stored as key field in parent object)
            if ($parentAction === self::ACTION_DELETE && $type === ClassMetadataInfo::MANY_TO_ONE) {
                $action = self::ACTION_NOTHING;
            } else {
                $action = self::ACTION_ERROR;
            }
        } else {
            $action = self::ACTION_CHECK_OWNER;
        }
        return $action;
    }

    /**
     * Returns the action that will be performed if the reference record will be deleted
     *
     * @param string|null $parentAction
     * @return string
     */
    public function getPerformedAction(?string $parentAction): string
    {
        if (null !== $parentAction && !self::actionRequiresCheck($parentAction)) {
            return self::ACTION_NOTHING;
        }
        if (null === $this->actionPerformed) {
            $action = $this->getActionBasedOnMapping($parentAction);
            if ($action === self::ACTION_CHECK_OWNER && !$this->isMappedOneSided()) {
                $referenceMap = $this->getTargetEntityReferenceMap();
                $referenceProperty = $referenceMap->getInversePropertyReference($this);
                if (null !== $referenceProperty) {
                    $action = $referenceProperty->getPerformedAction($action);
                } else {
                    $action = self::ACTION_ERROR;
                }
            }
            $this->actionPerformed = $action;
        }
        return $this->actionPerformed;
    }

    /**
     * Returns the action that will be performed if the reference record will be deleted
     *
     * @param BaseEntityInterface|object|null $object
     * @param string|null $parentAction
     * @return string
     */
    public function getPerformedObjectAction($object, ?string $parentAction): string
    {
        if (null === $object) {
            return self::ACTION_NOTHING;
        }
        $objectKey = get_class($object) . '::' . $object->getId();
        if (!isset($this->objectActions[(string)$parentAction][$objectKey])) {
            $this->objectActions[(string)$parentAction][$objectKey] = $this->determineObjectAction($object, $parentAction);
        }

        return $this->objectActions[(string)$parentAction][$objectKey];
    }

    /**
     * Determines the action to be performed for the given object
     *
     * @param BaseEntityInterface|object $object
     * @param string|null $parentAction
     * @return string
     */
    private function determineObjectAction($object, ?string $parentAction): string
    {
        $action = $this->getPerformedAction($parentAction);
        if ($action === self::ACTION_CHECK_OWNER && !$this->isMappedOneSided()) {
            $referenceMap = $this->getTargetEntityReferenceMap();
            $referenceProperty = $referenceMap->getPropertyReference($this->name);
            if (null !== $referenceProperty) {
                $values = $referenceProperty->getObjectIterableValue($object);
                $childAction = null;
                foreach ($values as $childObject) {
                    $childAction = $referenceProperty->getPerformedObjectAction($childObject, $action);
                    if ($childAction === self::ACTION_ERROR) {
                        break;
                    }
                }
                if (null === $childAction) {
                    $childAction = self::ACTION_ERROR;
                }
                $action = $childAction;
            }
        }
        return $action;
    }

    /**
     * Returns true if the property needs to be checked depending on the given action
     * @param string $action
     * @return bool
     */
    public static function actionRequiresCheck(string $action): bool
    {
        // self::ACTION_ERROR does not require more checks because we already have an error
        return in_array($action, [self::ACTION_DELETE, self::ACTION_UNKNOWN, self::ACTION_CHECK_OWNER], true);
    }

    /**
     * Returns true, if sql join column is configured to set value to null on delete
     *
     * @param array|null $mapping
     * @return bool
     */
    protected function isSetNull(?array $mapping = null): bool
    {
        $checkMapping = $mapping ?? $this->mapping;
        return $this->isOnDeleteEqualsValue($checkMapping, self::ON_DELETE_SET_NULL)
            || (!empty($checkMapping['joinTable']) && !empty($checkMapping['joinTable']['joinColumns'][0]["nullable"]));
    }

    /**
     * Returns true, if sql join column is configured to set value to null on delete
     *
     * @param array|null $mapping
     * @param string $equalsValue
     * @return bool
     */
    protected function isOnDeleteEqualsValue(array $mapping, string $equalsValue): bool
    {
        if (!empty($mapping['joinTable'])) {
            return $this->isOnDeleteEqualsValue($mapping['joinTable'], $equalsValue);
        }
        if (!empty($mapping['joinColumns'][0]["onDelete"])) {
            return $mapping['joinColumns'][0]['onDelete'] === $equalsValue;
        }
        return false;
    }


    /**
     * Returns true, if current entity property is owner of relation
     *
     * @return bool
     */
    protected function isCascadeRemove(): bool
    {
        return (bool)$this->mapping['isCascadeRemove'];
    }

    /**
     * Returns true, if current entity property is owner of relation
     *
     * @return bool
     */
    protected function isOwning(): bool
    {
        return (bool)$this->mapping['isOwningSide'];
    }

    /**
     * @return string|null
     */
    public function getEntityClass(): ?string
    {
        return $this->getTargetEntityReferenceMap()->getEntityClass();
    }

    /**
     * @return string|null
     */
    public function getEntityLabelClass(): ?string
    {
        return $this->mapping['sourceEntity'];
    }

    /**
     * Returns the property value for the current property and the given object
     *
     * @param BaseEntityInterface|object|null $object
     * @return mixed|null
     */
    public function getObjectValue($object)
    {
        if (is_object($object)) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            return $propertyAccessor->getValue($object, $this->name);
        }
        return null;
    }

    /**
     * @param BaseEntityInterface|object $parentObject
     * @return Collection|array|bool|mixed
     */
    public function getObjectIterableValue($parentObject)
    {
        if (null !== $this->fieldDescription) {
            $value = $this->fieldDescription->getValue($parentObject);
        } else {
            $value = $this->getObjectValue($parentObject);
        }
        if (!is_iterable($value)) {
            return null !== $value ? [$value] : [];
        }
        return $value;
    }

    /**
     * @return EntityReferenceMap
     */
    public function getTargetEntityReferenceMap(): EntityReferenceMap
    {
        return $this->targetEntityReferenceMap;
    }

    /**
     * @param EntityReferenceMap $targetEntityReferenceMap
     */
    public function setTargetEntityReferenceMap(EntityReferenceMap $targetEntityReferenceMap): void
    {
        $this->targetEntityReferenceMap = $targetEntityReferenceMap;
    }

    /**
     * Returns true if the given property needs to be checked;
     * Only returns false, if the given property reference is the inversion of the current property and the
     * reference will be deleted
     *
     * @param EntityReferenceProperty $property
     * @param string|null $parentAction
     * @return bool
     */
    public function requiresCheck(EntityReferenceProperty $property, ?string $parentAction = null): bool
    {
        $sourceMapping = $property->getMapping();
        $targetMapping = $this->getMapping();
        $isInverseMapping = $sourceMapping['targetEntity'] === $targetMapping['sourceEntity']
            && ($sourceMapping['fieldName'] === $targetMapping['inversedBy']
                || $sourceMapping['fieldName'] === $targetMapping['mappedBy']
                || $sourceMapping['mappedBy'] === $targetMapping['fieldName']
                || $sourceMapping['inversedBy'] === $targetMapping['fieldName']);
        return !$isInverseMapping || self::actionRequiresCheck($parentAction);
    }

    /**
     * @return bool
     */
    public function isMappedOneSided(): bool
    {
        return $this->isMappedOneSided;
    }

    /**
     * @param bool $isMappedOneSided
     */
    public function setIsMappedOneSided(bool $isMappedOneSided): void
    {
        $this->isMappedOneSided = $isMappedOneSided;
    }

    /**
     * Returns the current level of this property as a string
     *
     * @param string $action
     * @return int
     */
    public function getWarnLevel(string $action): int
    {
        switch ($action) {
            case self::ACTION_DELETE:
            case self::ACTION_SET_NULL:
                $warnLevel = self::LEVEL_WARNING;
                break;
            case self::ACTION_REMOVE_MM_REFERENCE:
            case self::ACTION_NOTHING:
                $warnLevel = self::LEVEL_OK;
                break;
            default:
                $warnLevel = self::LEVEL_DANGER;
        }

        return $warnLevel;
    }

    /**
     * Returns the current level of this property as a string
     *
     * @param string $action
     * @return string
     */
    public function getLevelAsString(string $action): string
    {
        $warnLevel = $this->getWarnLevel($action);
        if ($warnLevel === self::LEVEL_OK) {
            return 'success';
        }
        if ($warnLevel === self::LEVEL_WARNING) {
            return 'warning';
        }
        return 'danger';
    }

    /**
     * @return string
     */
    public function getActionPerformed(): string
    {
        return $this->actionPerformed;
    }

    /**
     * @param string $actionPerformed
     */
    public function setActionPerformed(string $actionPerformed): void
    {
        $this->actionPerformed = $actionPerformed;
    }
}
