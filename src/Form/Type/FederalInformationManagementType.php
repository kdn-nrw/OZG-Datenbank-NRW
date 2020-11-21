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

use App\Entity\FederalInformationManagementType as FederalInformationManagementEntity;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FederalInformationManagementType extends AbstractType
{
    /**
     * {@inheritdoc}
     * @noinspection PhpUnusedParameterInspection
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dataType', ChoiceType::class, [
                'choices' => array_flip(\App\Entity\FederalInformationManagementType::$mapTypes),
                'attr' => [
                    'class' => 'form-control',
                    'data-sonata-select2' => 'false'
                ],
                'label' => false,
                'required' => true,
                'disabled' => true,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'app.service.fim.entity.status',
                'choices' => array_flip(FederalInformationManagementEntity::$statusChoices),
                'required' => true,
                'expanded' => true,
                'choice_attr' => static function($choice, $key, $value) {
                    return ['class' => 'fim-status fim-status-' . $value];
                },
            ])
            ->add('notes', SimpleFormatterType::class, [
                'label' => 'app.service.fim.entity.notes',
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
            'data_class' => FederalInformationManagementEntity::class,
        ]);
    }


}