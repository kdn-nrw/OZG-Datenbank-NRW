<?php

namespace App\Admin;

use App\Admin\Traits\LaboratoryTrait;
use App\Entity\Priority;
use App\Entity\Status;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use LaboratoryTrait;
    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.service.entity.service_system_situation' => 'app.service_system.entity.situation',
        'app.service.entity.service_system_service_key' => 'app.service_system.entity.service_key',
        'app.service.entity.service_system_priority' => 'app.service_system.entity.priority',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $hideFields = $this->getFormHideFields();
        $formMapper
            ->with('app.service.tabs.general', [
                'label' => 'app.service.tabs.general',
                'tab' => true,
            ])
                ->with('app.service.groups.general', [
                    'label' => false,
                    'box_class' => 'box-tab',
                ])
                    ->add('name', TextareaType::class, [
                        'required' => true,
                    ])
                    ->add('serviceKey', TextType::class, [
                        'required' => true,
                    ]);

        if (!in_array('serviceSystem', $hideFields)) {
            $formMapper->add('serviceSystem', ModelAutocompleteType::class, [
                'property' => 'name',
                'required' => true,
            ]);
        }
        $this->addLaboratoriesFormFields($formMapper);
        $formMapper
                    ->add('status', ModelType::class, [
                        'btn_add' => false,
                        'required' => true,
                        'choice_translation_domain' => false,
                    ])
                    ->add('serviceType', TextType::class, [
                        'required' => true,
                    ])
                    ->add('legalBasis', TextareaType::class, [
                        'required' => false,
                    ])
                    ->add('laws', TextareaType::class, [
                        'required' => false,
                    ])
                    ->add('lawShortcuts', TextType::class, [
                        'required' => false,
                    ])
                    ->add('relevance1', BooleanType::class, [
                        'required' => false,
                    ])
                    ->add('relevance2', BooleanType::class, [
                        'required' => false,
                    ])
                ->end()
            ->end();
        if (!in_array('serviceSolutions', $hideFields)) {
            $formMapper->tab('app.service.tabs.service_solutions', [
                'label' => 'app.service.tabs.service_solutions',
                'box_class' => 'box-tab',
            ])
                ->with('app.service.entity.service_solutions', [
                    'label' => false,
                    'box_class' => 'box-tab',
                ])
                ->add('serviceSolutions', CollectionType::class, [
                    //'label' => false,
                    'type_options' => [
                        'delete' => true,
                    ],
                    'by_reference' => false,
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    //'sortable' => 'position',
                    'ba_custom_hide_fields' => ['service'],
                ])
                ->end()
            ->end();
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('serviceKey');
        $datagridMapper->add('serviceType');
        $datagridMapper->add('serviceSystem',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSystem.serviceKey',
            null,
            ['label' => 'app.service_system.entity.service_key']
        );
        $datagridMapper->add('status');
        $this->addLaboratoriesDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('serviceKey')
            ->add('serviceSystem', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystem'],
                ]
            ])
            ->add('serviceType')
            ->add('lawShortcuts')
            ->add('relevance1')
            ->add('relevance2')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ]);
        $this->addDefaultListActions($listMapper);
    }

    public function getExportFields()
    {
        return [
            'serviceSystem.situation', 'serviceSystem', 'serviceSystem.serviceKey', 'name',
            'serviceKey', 'serviceType', 'lawShortcuts', 'relevance1', 'relevance2', 'status'
        ];
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
            ->add('serviceSystem', null, [
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ])
            ->add('serviceType', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('legalBasis', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('laws', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('lawShortcuts', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('relevance1', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('relevance2', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.priority', 'choice', [
                'label' => 'app.service_system.entity.priority',
                'editable' => true,
                'class' => Priority::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.situation', null, [
                'label' => 'app.service_system.entity.situation',
            ])
            ->add('serviceSystem.situation.subject', null, [
                'label' => 'app.situation.entity.subject',
            ]);
        $this->addLaboratoriesShowFields($showMapper);
    }
}
