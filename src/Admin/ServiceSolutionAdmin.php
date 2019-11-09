<?php

namespace App\Admin;

use App\Entity\Maturity;
use App\Entity\ServiceSolution;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;


class ServiceSolutionAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('service', ModelAutocompleteType::class, [
                'property' => 'name',
                'required' => true,
            ], [
                'admin_code' => \App\Admin\Frontend\ServiceAdmin::class
            ])/*
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
            ])*/
            ->add('maturity', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
            ])
            /*->add('description', TextareaType::class, [
                'required' => false,
            ])*/
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('service',
            null,
            [
                'admin_code' => \App\Admin\Frontend\ServiceAdmin::class
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('solution',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        /*$datagridMapper->add('description');
        $datagridMapper->add('status');*/
        $datagridMapper->add('maturity');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('service', null, [
                'admin_code' => \App\Admin\Frontend\ServiceAdmin::class
            ])
            ->add('solution')
            /*->add('description')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])*/
            ->add('maturity')
            ->add('_action', null, [
                'label' => 'app.common.actions',
                'translation_domain' => 'messages',
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
            ->add('service', [
                'admin_code' => \App\Admin\Frontend\ServiceAdmin::class
            ])
            ->add('solution')
            ->add('maturity')/*
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])*/
        ;
    }

    public function getNewInstance()
    {
        /** @var ServiceSolution $object */
        $object = parent::getNewInstance();
        $defaultMaturity = $this->getModelManager()->find(Maturity::class, Maturity::DEFAULT_ID);
        if (null !== $defaultMaturity) {
            /** @var Maturity $defaultMaturity */
            $object->setMaturity($defaultMaturity);
        }

        return $object;
    }
}
