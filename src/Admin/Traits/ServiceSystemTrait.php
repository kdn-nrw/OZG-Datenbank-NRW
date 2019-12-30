<?php

namespace App\Admin\Traits;

use App\Admin\ServiceSystemAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait ServiceSystemTrait
{
    protected function addServiceSystemsFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('serviceSystems', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ],
                [
                    'admin_code' => ServiceSystemAdmin::class,
                ]
            );
    }

    protected function addServiceSystemsDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('serviceSystems',
            null, [
                'admin_code' => ServiceSystemAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addServiceSystemsListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('serviceSystems', null,[
                'admin_code' => ServiceSystemAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addServiceSystemsShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('serviceSystems', null,[
                'admin_code' => ServiceSystemAdmin::class,
                //'template' => 'General/Show/show-serviceSystems.twig',
            ]);
    }
}