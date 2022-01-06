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

use App\Admin\ContextAwareAdminInterface;
use App\Model\ReferenceSettings;
use App\Service\InjectAdminManagerTrait;
use App\Service\InjectApplicationContextHandlerTrait;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReferenceSettingsExtension extends AbstractExtension
{
    use InjectAdminManagerTrait;

    use InjectApplicationContextHandlerTrait;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_is_backend', [$this, 'getAdminContextIsBackend']),
            new TwigFunction('app_get_reference_settings', [$this, 'getReferenceSettings']),
            new TwigFunction('app_get_entity_label', [$this, 'getClassPropertyLabel']),
            new TwigFunction('app_object_field_description_meta', [$this, 'getObjectFieldDescriptionMeta']),
        ];
    }

    /**
     * Returns true if the current application context is "backend"
     *
     * @return bool
     */
    public function getAdminContextIsBackend(): bool
    {
        return $this->applicationContextHandler->isBackend();
    }

    /**
     * Creates the label key for the given
     * @param string $entityClass
     * @param string $property
     * @return string
     */
    public function getClassPropertyLabel(string $entityClass, string $property = ''): string
    {
        return PrefixedUnderscoreLabelTranslatorStrategy::getClassPropertyLabel($entityClass, $property);
    }

    /**
     * @param object $object
     * @param FieldDescriptionInterface $fieldDescription
     * @return ReferenceSettings
     */
    public function getObjectFieldDescriptionMeta($object, FieldDescriptionInterface $fieldDescription): ReferenceSettings
    {
        $propertyConfiguration = $this->adminManager->getConfigurationForEntityProperty($object, $fieldDescription->getName());
        if ($propertyConfiguration['entity_class']) {
            return $this->getReferenceSettings($propertyConfiguration['entity_class'], $fieldDescription);
        }
        return $this->getDefaultReferenceSettings(get_class($object));
    }

    /**
     * @param object|string $objectOrClass The entity or entity class name for which the settings are loaded
     * @param FieldDescriptionInterface|null $fieldDescription The optional field description (not set for custom fields)
     * @return ReferenceSettings
     */
    public function getReferenceSettings(
        $objectOrClass,
        ?FieldDescriptionInterface $fieldDescription = null
    ): ReferenceSettings
    {
        if (is_object($objectOrClass)) {
            if ($objectOrClass instanceof Collection) {
                $firstItem = $objectOrClass->first();
                $entityClass = get_class($firstItem);
            } else {
                $entityClass = get_class($objectOrClass);
            }
            $entityClass = ClassUtils::getRealClass($entityClass);
        } else {
            $entityClass = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;
        }
        $isBackendMode = $this->applicationContextHandler->isBackend();
        $editRouteName = 'edit';
        if (null !== $fieldDescription && $fieldDescription->hasAssociationAdmin()
            && (null !== $tmpFieldAdmin = $fieldDescription->getAssociationAdmin())
            && $tmpFieldAdmin->getClass() === $entityClass) {
            $refAdmin = $tmpFieldAdmin;
            $editRouteName = $fieldDescription->getOption('route')['name'];
        } else {
            $adminClass = $this->adminManager->getAdminClassForEntityClass($entityClass);
            $refAdmin = $adminClass ? $this->adminManager->getAdminInstance($adminClass) : null;
        }
        if (null !== $refAdmin) {
            if ($refAdmin instanceof ContextAwareAdminInterface) {
                $settings = $refAdmin->getReferenceSettings($this->applicationContextHandler, $editRouteName);
            } else {
                $showRouteName = 'show';
                /** @var AbstractAdmin $refAdmin */
                $settings = new ReferenceSettings();
                $settings->setAdmin($refAdmin);
                $createShowLink = $refAdmin->hasRoute($showRouteName) && $refAdmin->hasAccess($showRouteName);
                $createEditLink = $isBackendMode && $refAdmin->hasRoute($editRouteName) && $refAdmin->hasAccess($editRouteName);
                $settings->setShow($createShowLink, $showRouteName);
                $settings->setEdit($createEditLink, $editRouteName);
                $settings->setLabelPrefix(PrefixedUnderscoreLabelTranslatorStrategy::getClassLabelPrefix($entityClass));
            }
        } else {
            $settings = $this->getDefaultReferenceSettings($entityClass);
        }
        $settings->setEntityClass($entityClass);
        return $settings;
    }

    /**
     * Creates default reference settings
     * @param string $entityClass
     * @return ReferenceSettings
     */
    private function getDefaultReferenceSettings(string $entityClass): ReferenceSettings
    {
        $settings = new ReferenceSettings();
        $settings->setShow(false);
        $settings->setEdit(false);
        $label = $this->getClassPropertyLabel($entityClass);
        $settings->setListTitle($label);
        $settings->setLabelPrefix(PrefixedUnderscoreLabelTranslatorStrategy::getClassLabelPrefix($entityClass));
        return $settings;
    }
}
