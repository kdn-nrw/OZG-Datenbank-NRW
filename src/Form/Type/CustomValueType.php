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

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Configuration\CustomField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomValueType extends AbstractType
{
    public const FIELD_PREFIX = 'dynamicCustomValue';

    use InjectManagerRegistryTrait;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customCategoryCount = null;
        $entityClass = $options['entity_class'];
        $this->addCustomFieldsForEntity($builder, $entityClass);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $entityClass
     */
    private function addCustomFieldsForEntity(FormBuilderInterface $builder, string $entityClass): void
    {
        $repository = $this->getEntityManager()->getRepository(CustomField::class);
        $customFields = $repository->findBy(['recordType' => $entityClass, 'hidden' => false], ['position' => 'ASC', 'id' => 'ASC']);
        foreach ($customFields as $customField) {
            /** @var CustomField $customField */
            $fieldType = $customField->getFieldType() ?? TextType::class;
            $builder->add(self::FIELD_PREFIX . '_' . $customField->getId(), $fieldType, [
                'label' => $customField->getFieldLabel(),
                'translation_domain' => false,
                'required' => $customField->isRequired(),
                'mapped' => false,
                'attr' => [
                    'placeholder' => $customField->getFieldLabel() . '',
                    'class' => 'form-control',
                ],
            ]);
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
