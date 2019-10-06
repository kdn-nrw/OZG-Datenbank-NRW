<?php

namespace App\Admin;

use App\Entity\Status;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceSystemAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.service_system.';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, [
                'label' => $this->labelGroup . 'name',
                'required' => true,
            ])
            ->add('serviceKey', TextType::class, [
                'label' => $this->labelGroup . 'service_key',
                'required' => true,
            ])
            ->add('situation', ModelType::class, [
                'btn_add' => false,
                'label' => $this->labelGroup . 'situation',
            ])
            ->add('status', ModelType::class, [
                'label' => $this->labelGroup . 'status',
                'btn_add' => false,
                'required' => true,
            ])
            ->add('priority', ModelType::class, [
                'label' => $this->labelGroup . 'priority',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
            ])
            ->add('execution', TextType::class, [
                'label' => $this->labelGroup . 'execution',
                'required' => false,
            ])
            ->add('contact', TextareaType::class, [
                'label' => $this->labelGroup . 'contact',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->labelGroup . 'description',
                'required' => false,
            ])
            ->add('services', CollectionType::class, [
                'label' => $this->labelGroup . 'services',
                'type_options' => [
                    'delete' => true,
                ],
                'by_reference' => false,
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name',
            null,
            ['label' => $this->labelGroup . 'name']
        );
        $datagridMapper->add('serviceKey', null, [
            'label' => $this->labelGroup . 'service_key'
        ]);
        $datagridMapper->add('situation',
            null,
            ['label' => $this->labelGroup . 'situation'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('situation.subject',
            null,
            ['label' => 'app.entity.situation.subject'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('priority',
            null,
            ['label' => $this->labelGroup . 'priority'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status',
            null,
            ['label' => $this->labelGroup . 'status']
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('serviceKey', null, [
                'label' => $this->labelGroup . 'service_key'
            ])
            ->add('situation', null, [
                'label' => $this->labelGroup . 'situation',
            ])
            ->add('situation.subject', null, [
                'label' => 'app.entity.situation.subject',
            ])
            ->add('priority', null, [
                'label' => $this->labelGroup . 'priority',
            ])
            ->add('status', 'choice', [
                'label' => $this->labelGroup . 'status',
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
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
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('serviceKey', null, [
                'label' => $this->labelGroup . 'service_key',
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('situation.subject', null, [
                'label' => 'app.entity.situation.subject',
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ])
            ->add('situation', null, [
                'label' => $this->labelGroup . 'situation',
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ])
            ->add('priority', null, [
                'label' => $this->labelGroup . 'priority',
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('status', 'choice', [
                'label' => $this->labelGroup . 'status',
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('description', null, [
                'label' => $this->labelGroup . 'description',
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ]);
    }
}
