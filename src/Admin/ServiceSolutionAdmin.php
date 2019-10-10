<?php

namespace App\Admin;

use App\Entity\Status;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;


class ServiceSolutionAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.service_solution.';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('service', ModelAutocompleteType::class, [
                'label' => $this->labelGroup . 'service',
                'property' => 'name',
                'required' => true,
            ])/*
            ->add('status', ModelType::class, [
                'label' => $this->labelGroup . 'status',
                'btn_add' => false,
                'required' => true,
            ])*/
            ->add('maturity', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'label' => $this->labelGroup . 'maturity',
            ])
            /*->add('description', TextareaType::class, [
                'label' => $this->labelGroup . 'description',
                'required' => false,
            ])*/
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('service',
            null,
            ['label' => $this->labelGroup . 'service'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('solution',
            null,
            ['label' => $this->labelGroup . 'solution'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        /*$datagridMapper->add('description',
            null,
            ['label' => $this->labelGroup . 'description']
        );
        $datagridMapper->add('status',
            null,
            ['label' => $this->labelGroup . 'status']
        );*/
        $datagridMapper->add('maturity',
            null,
            ['label' => $this->labelGroup . 'maturity']
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('service', null, [
                'label' => $this->labelGroup . 'service',
            ])
            ->add('solution', null, [
                'label' => $this->labelGroup . 'solution',
            ])
            /*->add('description', null, [
                'label' => $this->labelGroup . 'description',
            ])
            ->add('status', 'choice', [
                'label' => $this->labelGroup . 'status',
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])*/
            ->add('maturity', null, [
                'label' => $this->labelGroup . 'maturity',
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
            ->add('service', null, [
                'label' => $this->labelGroup . 'service',
            ])
            ->add('solution', null, [
                'label' => $this->labelGroup . 'solution',
            ])
            ->add('maturity', null, [
                'label' => $this->labelGroup . 'maturity',
            ])/*
            ->add('status', 'choice', [
                'label' => $this->labelGroup . 'status',
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])*/;
    }
}
