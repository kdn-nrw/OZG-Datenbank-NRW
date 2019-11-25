<?php

namespace App\Admin;

use App\Entity\Status;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceSystemAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    /**
     * @var string[]
     */
    protected $customLabels = [
        'entity.situation_subject' => 'app.situation.entity.subject',
    ];
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('serviceKey', TextType::class, [
                'required' => true,
            ])
            ->add('situation', ModelType::class, [
                'btn_add' => false,
            ])
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
            ])
            ->add('priority', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
            ])
            ->add('execution', TextType::class, [
                'required' => false,
            ])
            ->add('contact', TextareaType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('jurisdictions', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('services', CollectionType::class, [
                'type_options' => [
                    'delete' => true,
                ],
                'by_reference' => false,
            ], [
                'admin_code' => \App\Admin\ServiceAdmin::class,
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('serviceKey');
        $datagridMapper->add('jurisdictions',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('situation',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('situation.subject',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('priority',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('serviceKey')
            ->add('jurisdictions')
            ->add('situation')
            ->add('situation.subject')
            ->add('priority')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])
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
            ->add('name', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('serviceKey', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('jurisdictions')
            ->add('situation.subject', null, [
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ])
            ->add('situation', null, [
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ])
            ->add('priority', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('description', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ]);
    }
}
