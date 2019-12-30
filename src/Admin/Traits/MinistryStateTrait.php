<?php

namespace App\Admin\Traits;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait MinistryStateTrait
{
    protected function addStateMinistriesFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('stateMinistries', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ]
            );
    }

    protected function addStateMinistriesDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('stateMinistries',
            null, [
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addStateMinistriesListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('stateMinistries');
    }

    /**
     * @inheritdoc
     */
    public function addStateMinistriesShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('stateMinistries');
    }
}