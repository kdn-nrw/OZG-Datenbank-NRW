<?php

namespace App\Admin;

use App\Admin\Traits\MinistryStateTrait;
use App\Entity\Jurisdiction;
use App\Entity\Status;
use App\Form\DataTransformer\EntityCollectionToIdArrayTransformer;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceSystemAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use MinistryStateTrait;

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.service_system.entity.situation_subject' => 'app.situation.entity.subject',
    ];
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('app.service_system.tabs.general', ['tab' => true])
                ->with('app.service_system.groups.general', [
                    'label' => false,
                ])
                    ->add('name', TextType::class, [
                        'required' => true,
                    ])
                    ->add('serviceKey', TextType::class, [
                        'required' => true,
                    ])
                    ->add('situation', ModelType::class, [
                        'btn_add' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->add('status', ModelType::class, [
                        'btn_add' => false,
                        'required' => true,
                        'choice_translation_domain' => false,
                    ])
                    ->add('priority', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->add('contact', TextareaType::class, [
                        'required' => false,
                    ])
                    ->add('description', TextareaType::class, [
                        'required' => false,
                    ]);
        $formMapper->add('laboratories', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
        $formMapper->add('jurisdictions', ChoiceFieldMaskType::class, [
                        'choices' => [
                            'app.jurisdiction.entity.types.country' => Jurisdiction::TYPE_COUNTRY,
                            'app.jurisdiction.entity.types.state' => Jurisdiction::TYPE_STATE,
                            'app.jurisdiction.entity.types.commune' => Jurisdiction::TYPE_COMMUNE,
                        ],
                        'multiple' => true,
                        'map' => [
                            Jurisdiction::TYPE_COUNTRY => [],
                            Jurisdiction::TYPE_STATE => ['stateMinistries'],
                            Jurisdiction::TYPE_COMMUNE => ['stateMinistries'],
                        ],
                        'required' => true,
                    ]);/*
                    ->add('jurisdictions', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_translation_domain' => false,
                    ]);*/
        $formMapper->get('jurisdictions')->addModelTransformer(new EntityCollectionToIdArrayTransformer(
            $this->getModelManager(),
            Jurisdiction::class
        ));
        $this->addStateMinistriesFormFields($formMapper);
        $formMapper->end()
            ->end();
        $formMapper->tab('app.service_system.tabs.services')
                ->with('app.service_system.entity.services', [
                    'label' => false,
                    'box_class' => 'box-tab',
                    'translation_domain' => 'messages',
                ])
                    ->add('services', CollectionType::class, [
                        'label' => false,
                        'type_options' => [
                            'delete' => true,
                        ],
                        'by_reference' => false,
                    ], [
                        'admin_code' => ServiceAdmin::class,
                        'edit' => 'inline',
                        'inline' => 'natural',
                        'sortable' => 'position',
                        'ba_custom_hide_fields' => ['serviceSystem',],// 'serviceSolutions'
                    ])
                ->end()
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('serviceKey');
        $datagridMapper->add('laboratories',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
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
        $this->addStateMinistriesDatagridFilters($datagridMapper);
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
            ->add('references', 'string', [
                'label' => 'app.service_system.entity.references',
                'template' => 'ServiceSystemAdmin/list-references.html.twig',
            ]);
        $this->addDefaultListActions($listMapper);
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
            ->add('jurisdictions');
        $this->addStateMinistriesShowFields($showMapper);
        $showMapper->add('laboratories');
        $showMapper->add('situation.subject', null, [
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
