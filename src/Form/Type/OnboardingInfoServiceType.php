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


use App\Entity\Onboarding\CommuneInfo;
use App\Entity\Onboarding\OnboardingCommuneSolution;
use App\Entity\Solution;
use App\Entity\StateGroup\Commune;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OnboardingInfoServiceType
 */
class OnboardingInfoServiceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$parentAdmin = $options['parent_admin'];

        if ($options['enable_commune_info']) {
            $builder->add('communeInfo', EntityType::class, [
                'label' => 'app.onboarding_commune_solution.entity.commune_info',
                'class' => CommuneInfo::class,
                'row_attr' => [
                    'class' => 'form-group-col ct-col-75',
                ],
            ]);
        }
        if ($options['enable_commune']) {
            $builder->add('commune', EntityType::class, [
                'label' => 'app.onboarding_commune_solution.entity.commune',
                'class' => Commune::class,
                'row_attr' => [
                    'class' => 'form-group-col ct-col-75',
                ],
            ]);
        }
        $builder->add('solution', EntityType::class, [
            'label' => false,//'app.onboarding_commune_solution.entity.solution',
            'class' => Solution::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('s')
                    ->where('s.enabledMunicipalPortal = 1')
                    ->orderBy('s.name', 'ASC')
                    ->addOrderBy('s.id', 'ASC');
            },
            'disabled' => $options['enable_solution_readonly'],
            'row_attr' => [
                'class' => 'form-group-col ct-col-75',
            ],
        ]);
        $builder->add('enabledEpayment', CheckboxType::class, [
            'label' => 'app.onboarding_commune_solution.entity.enabled_epayment',
            'required' => false,
            'attr' => [
                'align_with_widget' => true,
            ],
            'row_attr' => [
                'class' => 'form-group-col',
            ],
        ]);
        /*
        $builder->add('enabledMunicipalPortal', CheckboxType::class, [
            'label' => 'app.onboarding_commune_solution.entity.enabled_municipal_portal',
            'required' => false,
            'attr' => [
                'align_with_widget' => true,
            ],
            'row_attr' => [
                'class' => 'form-group-col',
            ],
        ]);
        */
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OnboardingCommuneSolution::class,
            'parent_admin' => null,
            'enable_commune_info' => false,
            'enable_commune' => false,
            'enable_solution_readonly' => true,
        ]);
    }

}
