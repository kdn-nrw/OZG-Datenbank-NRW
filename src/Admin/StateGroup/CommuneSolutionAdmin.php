<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\StateGroup;

use App\Admin\AbstractAppAdmin;
use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\Traits\DatePickerTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class CommuneSolutionAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use DatePickerTrait;

    protected function configureFormFields(FormMapper $form)
    {
        if (!$this->isExcludedFormField('commune')) {
            $form
                ->add('commune', ModelAutocompleteType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => \App\Admin\StateGroup\CommuneAdmin::class
                ]);
        }
        if (!$this->isExcludedFormField('solution')) {
            $form
                ->add('solution', ModelAutocompleteType::class, [
                    'property' => ['name', 'description'],
                    'required' => true,
                ], [
                    'admin_code' => \App\Admin\SolutionAdmin::class
                ]);
        }
        $form
            ->add('solutionReady', ChoiceFieldMaskType::class, [
                'choices' => [
                    'app.commune_solution.entity.solution_ready_choices.no' => false,
                    'app.commune_solution.entity.solution_ready_choices.yes' => true,
                ],
                'map' => [
                    false => [],
                    true => ['solutionReadyAt'],
                ],
                'required' => false,
            ]);
        $this->addDatePickerFormField($form, 'solutionReadyAt', 5);
        $form
            ->add('connectionPlanned', ChoiceFieldMaskType::class, [
                'choices' => [
                    'app.commune_solution.entity.connection_planned_choices.no' => false,
                    'app.commune_solution.entity.connection_planned_choices.yes' => true,
                ],
                'map' => [
                    false => [],
                    true => ['connectionPlannedAt'],
                ],
                'required' => false,
            ]);
        $this->addDatePickerFormField($form, 'connectionPlannedAt', 5);

        $form
            ->add('specializedProcedure', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'choice_translation_domain' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $this->addDefaultDatagridFilter($filter, 'commune');
        $this->addDefaultDatagridFilter($filter, 'solution');
        /*$filter->add('description');
        $filter->add('status');*/
        $filter
            ->add('solutionReady', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => [
                        'app.commune_solution.entity.solution_ready_choices.no' => false,
                        'app.commune_solution.entity.solution_ready_choices.yes' => true,
                    ],
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    //'choice_translation_domain' => 'SonataAdminBundle',
                ],
                'field_type' => ChoiceType::class,
            ]);
        $filter
            ->add('connectionPlanned', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => [
                        'app.commune_solution.entity.connection_planned_choices.no' => false,
                        'app.commune_solution.entity.connection_planned_choices.yes' => true,
                    ],
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    //'choice_translation_domain' => 'SonataAdminBundle',
                ],
                'field_type' => ChoiceType::class,
            ]);
        $filter->add('specializedProcedure');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('commune', null, [
                'admin_code' => \App\Admin\StateGroup\CommuneAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => \App\Admin\SolutionAdmin::class
            ])
            ->add('solutionReady', TemplateRegistryInterface::TYPE_CHOICE, [
                'choices' => [
                    false => 'app.commune_solution.entity.connection_planned_choices.no',
                    true => 'app.commune_solution.entity.connection_planned_choices.yes',
                ],
            ])
            ->add('connectionPlanned', TemplateRegistryInterface::TYPE_CHOICE, [
                'choices' => [
                    false => 'app.commune_solution.entity.connection_planned_choices.no',
                    true => 'app.commune_solution.entity.connection_planned_choices.yes',
                ],
            ])
            ->add('specializedProcedure');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('commune', null, [
                'admin_code' => \App\Admin\StateGroup\CommuneAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => \App\Admin\SolutionAdmin::class
            ])
            ->add('description')
            ->add('solutionReady', TemplateRegistryInterface::TYPE_CHOICE, [
                'choices' => [
                    false => 'app.commune_solution.entity.connection_planned_choices.no',
                    true => 'app.commune_solution.entity.connection_planned_choices.yes',
                ],
            ]);
        $this->addDatePickersShowFields($show, 'solutionReadyAt', false);
        $show
            ->add('connectionPlanned', TemplateRegistryInterface::TYPE_CHOICE, [
                'choices' => [
                    false => 'app.commune_solution.entity.connection_planned_choices.no',
                    true => 'app.commune_solution.entity.connection_planned_choices.yes',
                ],
            ]);
        $this->addDatePickersShowFields($show, 'connectionPlannedAt', false);

        $show
            ->add('specializedProcedure')
            ->add('comment', TextareaType::class, [
                'required' => false,
            ]);
    }
}
