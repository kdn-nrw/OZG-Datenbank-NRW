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

use App\Admin\AbstractContextAwareAdmin;
use App\Model\ReferenceSettings;
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
     * RenderPageContentExtension constructor.
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_get_reference_settings', [$this, 'getReferenceSettings']),
        ];
    }

    /**
     * @param string $entityClass The entity class name for which the settings are loaded
     * @param string $context The application context (backend or frontend)
     * @param FieldDescriptionInterface|null $fieldDescription The optional field description (not set for custom fields)
     * @return ReferenceSettings
     */
    public function getReferenceSettings(string $entityClass, string $context, ?FieldDescriptionInterface $fieldDescription = null): ReferenceSettings
    {
        $refAdmin = null;
        $isBackendMode = $context === 'backend';
        $editRouteName = 'edit';
        if (null !== $fieldDescription && $fieldDescription->hasAssociationAdmin()) {
            $refAdmin = $fieldDescription->getAssociationAdmin();
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
                } else {
                    $refAdmin = $this->pool->getAdminByClass($objectAdminClasses[0]);
                }
            }
        }
        if (null !== $refAdmin) {
            if ($refAdmin instanceof AbstractContextAwareAdmin) {
                $settings = $refAdmin->getReferenceSettings($context, $editRouteName);
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
        }
        return $settings;
    }
}
