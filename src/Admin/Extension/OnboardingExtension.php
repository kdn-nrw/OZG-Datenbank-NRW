<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2023 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Admin\Extension;

use App\Admin\Onboarding\AbstractOnboardingAdmin;
use App\Entity\MetaData\AbstractMetaItem;
use App\Service\MetaData\InjectMetaDataManagerTrait;
use App\Util\SnakeCaseConverter;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Admin extension for onboarding admin form meta data
 */
class OnboardingExtension extends AbstractAdminExtension
{
    use InjectMetaDataManagerTrait;

    public function configureFormFields(FormMapper $form): void
    {
        $admin = $form->getAdmin();
        if ($admin instanceof AbstractOnboardingAdmin) {
            $this->initializeFormMetaData($admin);
        }
    }

    /**
     * Set custom labels and descriptions for form groups and tabs
     *
     * @param AbstractOnboardingAdmin $admin
     * @return void
     */
    final protected function initializeFormMetaData(AbstractOnboardingAdmin $admin): void
    {
        $metaItem = $this->metaDataManager->getObjectClassMetaData($admin->getClass());
        if (null !== $metaItem) {
            $data = [
                AbstractMetaItem::META_TYPE_GROUP => $admin->getFormGroups(),
                AbstractMetaItem::META_TYPE_TAB => $admin->getFormTabs()
            ];
            $domain = $admin->getTranslationDomain();
            $translator = $admin->getTranslator();//->trans($id, $parameters, $domain, $locale);
            foreach ($data as $metaType => &$metaTypeData) {
                if (empty($metaTypeData)) {
                    continue;
                }
                foreach ($metaTypeData as $name => &$options) {
                    $groupKey = SnakeCaseConverter::camelCaseToSnakeCase(str_replace('.', '_', $name));
                    $metaKey = $metaType . '_' . $groupKey;
                    $property = $metaItem->getMetaItemProperty($metaKey);
                    if (null !== $property) {
                        $description = $property->getDescription();
                        if ($options['label'] !== false && $labelKey = $property->getLabelKey()) {
                            $options['label'] = $translator->trans($labelKey, [], $domain);
                            $options['translation_domain'] = false;
                        }
                        if ($description) {
                            $options['description'] = $description;
                        }
                    }
                }
                unset($options);
            }
            unset($metaTypeData);
            $admin->setFormGroups($data[AbstractMetaItem::META_TYPE_GROUP]);
            $admin->setFormTabs($data[AbstractMetaItem::META_TYPE_TAB]);
        }
    }
}
