<?php

namespace App\Admin\Traits;

use App\Admin\PortalAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait PortalTrait
{
    protected function addPortalsFormFields(FormMapper $formMapper)
    {
        $formMapper->add('portals', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addPortalsDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('portals',
            null, [
                'admin_code' => PortalAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addPortalsListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('portals', null,[
                'admin_code' => PortalAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addPortalsShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('portals', null,[
                'admin_code' => PortalAdmin::class,
            ]);
    }
}