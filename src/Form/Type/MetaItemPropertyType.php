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

namespace App\Form\Type;

use App\Entity\MetaData\MetaItemProperty;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetaItemPropertyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('metaKey', ChoiceType::class, [
                'label' => 'app.meta_item_property.entity.meta_key',
                'choices' => array_flip(\App\Entity\FederalInformationManagementType::$mapTypes),
                'attr' => [
                    'class' => 'form-control',
                    'data-sonata-select2' => 'false'
                ],
                'required' => true,
                'disabled' => true,
            ])
            ->add('customLabel', TextType::class, [
                'label' => 'app.meta_item_property.entity.custom_label',
                'required' => false,
            ])
            ->add('description', SimpleFormatterType::class, [
                'label' => 'app.meta_item_property.entity.description',
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MetaItemProperty::class,
        ]);
    }


}