<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class PortalAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.portal.';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, [
                'label' => $this->labelGroup . 'name'
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->labelGroup . 'description',
                'required' => false,
            ])
            ->add('url', TextType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'url'
            ])
            ->add('serviceProvider', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'label' => $this->labelGroup . 'service_provider',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name',
            null,
            ['label' => $this->labelGroup . 'name']
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('description', null, [
                'label' => $this->labelGroup . 'description',
            ])
            ->add('url', null, [
                'label' => $this->labelGroup . 'url'
            ])
            ->add('serviceProvider', null, [
                'label' => $this->labelGroup . 'service_provider',
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ]);
    }
}
