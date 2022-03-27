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

namespace App\Service\Mailer;

use App\Entity\Base\BaseEntityInterface;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\PersonInterface;
use App\Entity\User;
use App\Model\EmailTemplate\AbstractTemplateModel;
use App\Service\InjectAdminManagerTrait;
use App\Translator\InjectTranslatorTrait;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use App\Util\SnakeCaseConverter;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as RoutingUrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Create event and registration markers
 */
class MarkerService
{
    use InjectTranslatorTrait;
    use InjectAdminManagerTrait;

    /**
     * @param AbstractTemplateModel $model
     * @return array The model markers
     */
    public function getModelMarkers(AbstractTemplateModel $model): array
    {
        $markers = [];
        $variables = $model->getVariables();
        foreach ($variables as $key => $variable) {
            if ($variable instanceof BaseEntityInterface) {
                $this->addEntityMarkers($markers, $variable, $model->getMarkerGroup());
            } else {
                $this->addFieldValueMarker($markers, $key, $variable, '');
            }
        }
        if (null !== $user = $model->getUser()) {
            $allowedFields = ['firstname', 'lastname', 'email', 'gender', 'organisation'];
            $this->addObjectMarkers($markers, $user, 'USER', $allowedFields);
        }
        return $markers;
    }

    /**
     * Returns a marker array for the given entity instance
     *
     * @param BaseEntityInterface $entity
     * @return array
     */
    public function getEntityMarkers(BaseEntityInterface $entity): array
    {
        $markers = [];
        $this->addEntityMarkers($markers, $entity);

        return $markers;
    }

    /**
     * Returns a marker array for the given entity instance
     *
     * @param array $markers
     * @param BaseEntityInterface|UserInterface $entity
     * @param string|null $markerGroup
     */
    private function addEntityMarkers(array &$markers, $entity, ?string $markerGroup = null): void
    {
        $ignoreFields = ['id', 'pid', 'hash', 'internalRemarks',];
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $entityClass = ClassUtils::getRealClass(get_class($entity));
        if (!$markerGroup) {
            $markerGroup = trim(str_replace(['\\', 'App_Entity'], ['_', ''], $entityClass), ' _');
            $markerGroup = strtoupper(SnakeCaseConverter::camelCaseToSnakeCase($markerGroup));
        }
        $admin = $this->adminManager->getAdminByEntityClass($entityClass);
        $urlEdit = '';
        $urlShow = '';
        if (null !== $admin && $admin->hasRoute('edit') && $entity->getId()) {
            $urlEdit = $admin->generateObjectUrl('edit', $entity, [], RoutingUrlGeneratorInterface::ABSOLUTE_URL);
        }
        if (null !== $admin && $admin->hasRoute('show') && $entity->getId()) {
            $urlShow = $admin->generateObjectUrl('show', $entity, [], RoutingUrlGeneratorInterface::ABSOLUTE_URL);
        }
        $classLabel = PrefixedUnderscoreLabelTranslatorStrategy::getClassLabelPrefix($entityClass, '') . 'object_name';
        $this->addFieldValueMarker($markers, 'classLabel', $classLabel, $markerGroup);
        $this->addFieldValueMarker($markers, 'adminEditUrl', $urlEdit, $markerGroup);
        $this->addFieldValueMarker($markers, 'adminShowUrl', $urlShow, $markerGroup);
        // ADMIN_EDIT_URL
        $entityProperties = $this->getEntityPropertyList($entity, $ignoreFields);
        foreach ($entityProperties as $propertyName) {
            if ($propertyAccessor->isReadable($entity, $propertyName)) {
                $fieldValue = $propertyAccessor->getValue($entity, $propertyName);
                $this->addFieldValueMarker($markers, $propertyName, $fieldValue, $markerGroup);
            }
        }

    }

    private function addObjectMarkers(array &$markers, $object, $markerGroup, array $allowedFields): void
    {
        $entityProperties = $this->getEntityPropertyList($object, [], $allowedFields);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($entityProperties as $propertyName) {
            if ($propertyAccessor->isReadable($object, $propertyName)) {
                $fieldValue = $propertyAccessor->getValue($object, $propertyName);
                $this->addFieldValueMarker($markers, $propertyName, $fieldValue, $markerGroup);
            }
        }
        if ($object instanceof PersonInterface) {
            $this->addGreetingMarker($markers, $object, $markerGroup . '_GREETING');
        } elseif ($object instanceof User) {
            $personGender = $object->getGender();
            $name = $object->getLastname();
            $namePersonal = $object->getFirstname();
            $title = '';
            $this->addGreetingMarkerByValues($markers, $personGender, $name, $namePersonal, $title, $markerGroup . '_GREETING');
        }
    }

    /**
     * Add greeting markers for persons
     * 
     * @param array $markers
     * @param PersonInterface $entity
     * @param string $markerGroup
     */
    private function addGreetingMarker(array &$markers, PersonInterface $entity, string $markerGroup = 'GREETING'): void
    {
        $personGender = $entity->getGender();
        $name = $entity->getLastName();
        $namePersonal = $entity->getFirstName();
        $title = '';
        if (!empty($personGender) && !empty($name) && method_exists($entity, 'getTitle')) {
            $title = (string) $entity->getTitle();
        }
        $this->addGreetingMarkerByValues($markers, $personGender, $name, $namePersonal, $title, $markerGroup);
    }

    private function addGreetingMarkerByValues(&$markers, $personGender, $name, $namePersonal, $title, string $markerGroup = 'GREETING'): void
    {
        switch ($personGender) {
            case PersonInterface::GENDER_MALE:
            case \Sonata\UserBundle\Model\UserInterface::GENDER_MALE:
                $labelKey = 'app.contact.entity.email_greeting_choices.male';
                $labelKeyPersonal = 'app.contact.entity.email_greeting_personal_choices.male';
                break;
            case PersonInterface::GENDER_FEMALE:
            case \Sonata\UserBundle\Model\UserInterface::GENDER_FEMALE:
                $labelKey = 'app.contact.entity.email_greeting_choices.female';
                $labelKeyPersonal = 'app.contact.entity.email_greeting_personal_choices.female';
                break;
            case PersonInterface::GENDER_OTHER:
                $labelKey = 'app.contact.entity.email_greeting_choices.other';
                $labelKeyPersonal = 'app.contact.entity.email_greeting_personal_choices.other';
                $name = trim($namePersonal . ' ' . $name);
                break;
            default:
                $labelKey = 'app.contact.entity.email_greeting_choices.unknown';
                $labelKeyPersonal = 'app.contact.entity.email_greeting_personal_choices.unknown';
                break;
        }
        $greetingLabel = $this->translator->trans($labelKey);
        $greetingLabelPersonal = $this->translator->trans($labelKeyPersonal);
        if (!empty($personGender) && !empty($name) && $title) {
            if (strpos($title, 'Dipl') !== false && strpos($title, 'Dr') === false) {
                $title = '';
            }
            if (!empty($title)) {
                $greetingLabel .= ' ' . $title;
            }
        }
        $greetingLabel .= ' ' . $name;
        $greetingLabelPersonal .= ' ' . ($namePersonal ?: $name);
        $markers['###' . $markerGroup . '###'] = $greetingLabel;
        $markers['###' . $markerGroup . '_PERSONAL###'] = $greetingLabelPersonal;
    }

    /**
     * Returns a map of the entity fields an the corresponding getter functions
     *
     * @param BaseEntityInterface|UserInterface $entity
     * @param array $ignoreFields List of fields to be ignored
     * @param array $allowedFields
     * @return array
     */
    private function getEntityPropertyList($entity, array $ignoreFields = [], array $allowedFields = []): array
    {
        $classMethods = get_class_methods($entity);
        $entityGetters = [];
        foreach ($classMethods as $methodName) {
            if (strpos($methodName, 'get') === 0) {
                $getter = $methodName;
                $fieldName = lcfirst(substr($getter, 3));
                if (!in_array($fieldName, $ignoreFields, false)
                    && (empty($allowedFields) || in_array($fieldName, $allowedFields, false))) {
                    $entityGetters[$fieldName] = $getter;
                }
            }
        }
        return array_keys($entityGetters);
    }

    /**
     * Adds a marker for the given field and the field value to the marker group
     *
     * @param array $markers The marker array
     * @param string $fieldName
     * @param mixed $fieldValue
     * @param string|null $markerGroup
     */
    private function addFieldValueMarker(array &$markers, $fieldName, $fieldValue, $markerGroup): void
    {
        if (is_object($fieldValue)) {
            if ($fieldValue instanceof DateTime) {
                $dateValue = $fieldValue->format('d.m.Y');
                $this->addMarker($markers, $markerGroup, $fieldName . '_date', $dateValue);
            }
            if ($fieldValue instanceof PersonInterface) {
                $this->addMarker($markers, $markerGroup, $fieldName . '_firstName', $fieldValue->getFirstName());
                $this->addMarker($markers, $markerGroup, $fieldName . '_lastName', $fieldValue->getLastName());
            }
        } elseif (stripos($fieldName, 'Label') !== false && strpos($fieldValue, 'app.') === 0) {
            $fieldValue = $this->translator->trans($fieldValue);
        }
        $convertedValue = $this->convertValueToString($fieldValue);
        $this->addMarker($markers, $markerGroup, $fieldName, $convertedValue);
    }

    /**
     * Convert the given field value to string
     *
     * @param mixed $fieldValue
     * @return string
     */
    private function convertValueToString($fieldValue): string
    {
        $convertedValue = $fieldValue;
        if (is_object($fieldValue)) {
            if ($fieldValue instanceof DateTime) {
                $convertedValue = $fieldValue->format('d.m.Y H:i');
            } elseif ($fieldValue instanceof Collection) {
                $subValues = [];
                foreach ($fieldValue as $childValue) {
                    $subValues[] = $this->convertValueToString($childValue);
                }
                $convertedValue = implode(', ', array_filter($subValues));
            } elseif ($fieldValue instanceof PersonInterface) {
                $convertedValue = trim($fieldValue->getFirstName() . ' ' . $fieldValue->getLastName());
            } elseif ($fieldValue instanceof NamedEntityInterface) {
                $convertedValue = $fieldValue->getName();
            } elseif (method_exists($fieldValue, 'getTitle')) {
                $convertedValue = $fieldValue->getTitle();
            }
        } elseif (is_iterable($fieldValue)) {
            $subValues = [];
            foreach ($fieldValue as $childValue) {
                $subValues[] = $this->convertValueToString($childValue);
            }
            $convertedValue = implode(', ', array_filter($subValues));
        }
        return (string)$convertedValue;
    }


    /**
     * Convert the given input in camel case format into underscore format
     *
     * @param array $markers The marker array
     * @param string|null $prefix The optional prefix value for the marker
     * @param string $fieldName
     * @param string $fieldValue
     */
    private function addMarker(array &$markers, $prefix, $fieldName, $fieldValue): void
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $fieldName, $matches);
        $ret = $matches[0];
        foreach ($matches[0] as $key => $match) {
            $matches[0][$key] = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        $markerKey = strtoupper(implode('_', $ret));
        if ($prefix) {
            $markerKey = $prefix . '_' . str_replace($prefix . '_', '', $markerKey);
        }
        $markerKey = trim($markerKey, ' _');
        $markers['###' . $markerKey . '###'] = $fieldValue;
    }
}
