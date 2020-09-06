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
use App\Service\ApplicationContextHandler;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Admin\Pool;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReferenceSettingsExtension extends AbstractExtension
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var ApplicationContextHandler
     */
    private $applicationContextHandler;

    /**
     * RenderPageContentExtension constructor.
     * @param Pool $pool
     * @param ApplicationContextHandler $applicationContextHandler
     */
    public function __construct(Pool $pool, ApplicationContextHandler $applicationContextHandler)
    {
        $this->pool = $pool;
        $this->applicationContextHandler = $applicationContextHandler;
    }

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
     * @param string $entityClass The entity class name for which the settings are loaded
     * @param FieldDescriptionInterface|null $fieldDescription The optional field description (not set for custom fields)
     * @return ReferenceSettings
     */
    public function getReferenceSettings(
        string $entityClass,
        ?FieldDescriptionInterface $fieldDescription = null
    ): ReferenceSettings
    {
        $refAdmin = null;
        $isBackendMode = $this->applicationContextHandler->isBackend();
        $editRouteName = 'edit';
        if (null !== $fieldDescription && $fieldDescription->hasAssociationAdmin()
            && (null !== $tmpFieldAdmin = $fieldDescription->getAssociationAdmin())
            && $tmpFieldAdmin->getClass() === $entityClass) {
            $refAdmin = $tmpFieldAdmin;
            $editRouteName = $fieldDescription->getOption('route')['name'];
        } else {
            $adminClasses = $this->pool->getAdminClasses();
            $objectAdminClasses = $adminClasses[ltrim($entityClass, '\\')] ?? null;
            if (!empty($objectAdminClasses)) {
                if (count($objectAdminClasses) > 1) {
                    $keyword = '\\Frontend\\';
                    foreach ($objectAdminClasses as $adminClass) {
                        if ($isBackendMode && strpos($adminClass, $keyword) === false) {
                            $refAdmin = $this->pool->getInstance($adminClass);
                            break;
                        }
                        if (!$isBackendMode && strpos($adminClass, $keyword) !== false) {
                            $refAdmin = $this->pool->getInstance($adminClass);
                            break;
                        }
                    }
                } elseif ($this->pool->hasAdminByClass($objectAdminClasses[0])) {
                    $refAdmin = $this->pool->getAdminByClass($objectAdminClasses[0]);
                }
            }
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
            }
        } else {
            $settings = new ReferenceSettings();
            $settings->setShow(false);
            $settings->setEdit(false);
            $label = $this->getClassPropertyLabel($entityClass);
            $settings->setListTitle($label);
        }
        return $settings;
    }
}
