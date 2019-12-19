<?php

namespace App\Admin\Traits;

use App\Admin\SolutionAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait SolutionTrait
{
    protected function addSolutionsFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('solutions', ModelType::class,
                [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ],
                [
                    'admin_code' => SolutionAdmin::class,
                ]
            );
    }

    protected function addSolutionsDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('solutions',
            null, [
                'admin_code' => SolutionAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addSolutionsListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('solutions', null, [
                'admin_code' => SolutionAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addSolutionsShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('solutions', null, [
                'admin_code' => SolutionAdmin::class,
                'template' => 'General/Show/show-solutions.twig',
            ]);
    }
}