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

use App\Entity\Onboarding\EpaymentProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EpaymentProjectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('providerType', ChoiceType::class, [
            'label' => 'app.epayment.entity.projects.provider_type',
            'choices' => EpaymentProject::$providerTypeChoices,
            'disabled' => true,
            'row_attr' => [
                'class' => 'form-group-col form-group-head',
            ],
        ]);
        $builder->add('projectEnvironment', ChoiceType::class, [
            'label' => 'app.epayment.entity.projects.project_environment',
            'choices' => EpaymentProject::$projectEnvironmentChoices,
            'disabled' => true,
            'row_attr' => [
                'class' => 'form-group-col form-group-head',
            ],
        ]);
        $builder->add('projectId', TextType::class, [
            'label' => 'app.epayment.entity.projects.project.id',
            'required' => false,
            'row_attr' => [
                'class' => 'form-group-col',
            ],
        ]);
        $builder->add('projectPassword', TextType::class, [
            'label' => 'app.epayment.entity.projects.project.password',
            'required' => false,
            'row_attr' => [
                'class' => 'form-group-col',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EpaymentProject::class,
            'parent_admin' => null,
        ]);
    }


}