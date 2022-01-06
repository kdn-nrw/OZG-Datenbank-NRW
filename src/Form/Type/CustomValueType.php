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

namespace App\Form\Type;

use App\Entity\Configuration\CustomField;
use App\Service\Configuration\InjectCustomFieldManagerTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomValueType extends AbstractType
{
    public const FIELD_PREFIX = 'dynamicCustomValue';

    use InjectCustomFieldManagerTrait;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityClass = $options['entity_class'];
        $this->addCustomFieldsForEntity($builder, $entityClass);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $entityClass
     */
    private function addCustomFieldsForEntity(FormBuilderInterface $builder, string $entityClass): void
    {
        $customFields = $this->customFieldManager->getCustomFieldsForRecordType($entityClass);
        foreach ($customFields as $customField) {
            /** @var CustomField $customField */
            $fieldType = $customField->getFieldType() ?? TextType::class;
            $placeholder = $customField->getPlaceholder() ?? $customField->getFieldLabel();
            $fieldOptions = [
                'label' => $customField->getFieldLabel(),
                'translation_domain' => false,
                'mapped' => false,
            ];
            if ($fieldType !== HiddenType::class) {
                $fieldOptions['required'] = $customField->isRequired();
                $fieldOptions['attr'] = [
                    'placeholder' => $placeholder . '',
                    'class' => 'form-control',
                ];
            }
            if (!empty($customField->getDescription())) {
                $fieldOptions['help'] = $customField->getDescription();
            }
            $builder->add(self::FIELD_PREFIX . '_' . $customField->getId(), $fieldType, $fieldOptions);
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'inherit_data' => true,
            'entity_class' => null,
        ]);
    }

}
