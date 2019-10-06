<?php

namespace App\Admin;

use App\Entity\Priority;
use App\Entity\Status;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.service.';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextareaType::class, [
                'label' => $this->labelGroup . 'name',
                'required' => true,
            ])
            ->add('serviceKey', TextType::class, [
                'label' => $this->labelGroup . 'service_key',
                'required' => true,
            ])
            ->add('serviceSystem', ModelAutocompleteType::class, [
                'label' => $this->labelGroup . 'service_system',
                'property' => 'name',
                'required' => true,
            ])
            ->add('status', ModelType::class, [
                'label' => $this->labelGroup . 'status',
                'btn_add' => false,
                'required' => true,
            ])
            ->add('serviceType', TextType::class, [
                'label' => $this->labelGroup . 'service_type',
                'required' => true,
            ])
            ->add('legalBasis', TextareaType::class, [
                'label' => $this->labelGroup . 'legal_basis',
                'required' => false,
            ])
            ->add('laws', TextareaType::class, [
                'label' => $this->labelGroup . 'laws',
                'required' => false,
            ])
            ->add('lawShortcuts', TextType::class, [
                'label' => $this->labelGroup . 'law_shortcuts',
                'required' => false,
            ])
            ->add('relevance1', BooleanType::class, [
                'label' => $this->labelGroup . 'relevance1',
                'required' => false,
            ])
            ->add('relevance2', BooleanType::class, [
                'label' => $this->labelGroup . 'relevance2',
                'required' => false,
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
        $datagridMapper->add('serviceType',
            null,
            ['label' => $this->labelGroup . 'service_type']
        );
        $datagridMapper->add('serviceSystem',
            null,
            ['label' => $this->labelGroup . 'service_system'],
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
            ->add('serviceSystem', null, [
                'label' => $this->labelGroup . 'service_system',
            ])
            ->add('serviceType', null, [
                'label' => $this->labelGroup . 'service_type',
            ])
            ->add('lawShortcuts', null, [
                'label' => $this->labelGroup . 'law_shortcuts',
            ])
            ->add('relevance1', null, [
                'label' => $this->labelGroup . 'relevance1',
                'editable' => true,
            ])
            ->add('relevance2', null, [
                'label' => $this->labelGroup . 'relevance2',
                'editable' => true,
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
            ->add('serviceSystem', null, [
                'label' => $this->labelGroup . 'service_system',
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ])
            ->add('serviceType', null, [
                'label' => $this->labelGroup . 'service_type',
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('legalBasis', null, [
                'label' => $this->labelGroup . 'legal_basis',
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('laws', null, [
                'label' => $this->labelGroup . 'laws',
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('lawShortcuts', null, [
                'label' => $this->labelGroup . 'law_shortcuts',
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('relevance1', null, [
                'label' => $this->labelGroup . 'relevance1',
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('relevance2', null, [
                'label' => $this->labelGroup . 'relevance2',
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('status', 'choice', [
                'label' => $this->labelGroup . 'status',
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.priority', 'choice', [
                'label' => 'app.entity.service_system.priority',
                'editable' => true,
                'class' => Priority::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.situation', null, [
                'label' => 'app.entity.service_system.situation',
            ])
            ->add('serviceSystem.situation.subject', null, [
                'label' => 'app.entity.situation.subject',
            ]);
    }
}
