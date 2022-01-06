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

declare(strict_types=1);

namespace App\Admin\Extension;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Onboarding\OnboardingCustomValue;
use App\Form\DataMapper\CustomValueDataMapper;
use App\Form\Type\CustomValueType;
use App\Service\Configuration\InjectCustomFieldManagerTrait;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Admin extension for configuring routes in the frontend
 */
class CustomFieldExtension extends AbstractAdminExtension
{
    use InjectCustomFieldManagerTrait;
    use InjectManagerRegistryTrait;

    public function configureFormFields(FormMapper $form)
    {
        $admin = $form->getAdmin();
        $entityClass = $admin->getClass();
        $customFields = $this->customFieldManager->getCustomFieldsForRecordType($entityClass);
        if (!empty($customFields)) {
            if ($form->hasOpenTab()) {
                $form->end();
            }
            $keys = $form->keys();
            $addTab = true;
            // Optional label for custom field tab/group, e.g. app.epayment.tabs.custom_fields
            $customLabelKey = PrefixedUnderscoreLabelTranslatorStrategy::getClassLabelPrefix($entityClass, 'tabs') . 'custom_fields';
            if ($customLabelKey !== $admin->getTranslator()->trans($customLabelKey)) {
                $labelKey = $customLabelKey;
            } else {
                $labelKey = 'app.custom_field.tabs.custom_fields';
            }
            if ($addTab) {
                $form->tab('CustomFields', [
                    'label' => $labelKey,
                ]);
            }
            $form->with('custom_field_group', [
                'label' => $addTab ? false : $labelKey,
                'class' => 'col-xs-12',
            ]);
            $form->add('dynamicCustomValues', CustomValueType::class, [
                'label' => false,
                'entity_class' => $entityClass,
            ]);
            $form->getFormBuilder()->setDataMapper(new CustomValueDataMapper(
                $this->getEntityManager(),
                OnboardingCustomValue::class
            ));
            $form->end();
            if ($addTab) {
                $form->end();
            }
        }
    }
}
