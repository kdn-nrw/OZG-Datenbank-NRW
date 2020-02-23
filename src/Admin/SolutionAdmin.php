<?php

namespace App\Admin;

use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Entity\Status;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class SolutionAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use CommuneTrait;
    use ContactTrait;
    use ServiceSystemTrait;

    protected $datagridValues = [

        // display the first page (default = 1)
        '_page' => 1,

        // reverse order (default = 'ASC')
        '_sort_order' => 'DESC',

        // name of the ordered field (default = the model's id field, if any)
        '_sort_by' => 'id',
    ];

    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && $action !== 'edit') {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        if (null !== $admin) {
            $id = $admin->getRequest()->get('id');

            $menu->addChild('app.solution.actions.show', [
                'uri' => $admin->generateUrl('show', ['id' => $id])
            ]);

            if ($this->isGranted('EDIT')) {
                $menu->addChild('app.solution.actions.edit', [
                    'uri' => $admin->generateUrl('edit', ['id' => $id])
                ]);
            }

            if ($this->isGranted('LIST')) {
                $menu->addChild('app.solution.actions.list', [
                    'uri' => $admin->getChild(ServiceSolutionAdmin::class)->generateUrl('list', ['id' => $id])
                ]);
            }
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('app.solution.tabs.general', ['tab' => true])
                ->with('general', [
                    'label' => false,
                ])
                    ->add('serviceProvider', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->add('customProvider', TextType::class, [
                        'required' => false,
                    ])
                    ->add('status', ModelType::class, [
                        'btn_add' => false,
                        'required' => true,
                        'choice_translation_domain' => false,
                    ])
                    ->add('name', TextType::class, [
                        'required' => false,
                    ])
                    ->add('description', TextareaType::class, [
                        'required' => false,
                    ])
                    ->add('url', UrlType::class, [
                        'required' => false
                    ]);
            $this->addContactsFormFields($formMapper, true, false, 'solutionContacts');
        $formMapper->add('isPublished', CheckboxType::class, [
                        'required' => false,
                    ])
                    /*
                    ->add('serviceSolutions', ModelType::class, [
                        'expanded' => true,
                        'required' => false,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_translation_domain' => false,
                    ])*/
                ->end()
            ->end()
            ->tab('app.solution.tabs.relations')
                ->with('relations', [
                    'label' => false,
                ])
                    ->add('portals', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->add('communeType', ChoiceFieldMaskType::class, [
                        'choices' => [
                            'app.solution.entity.commune_type_all' => 'all',
                            'app.solution.entity.commune_type_selected' => 'selected',
                        ],
                        'map' => [
                            'all' => [],
                            'selected' => ['communes'],
                        ],
                        'required' => true,
                    ]);
                $this->addCommunesFormFields($formMapper);
                $formMapper
                    ->add('specializedProcedures', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->add('formServers', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->add('paymentTypes', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->add('authentications', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->add('analogServices', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->add('openDataItems', ModelType::class, [
                        'btn_add' => false,
                        'placeholder' => '',
                        'required' => false,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_translation_domain' => false,
                    ])
                ->end()
            ->end()
            ->tab('app.solution.tabs.services')
                ->with('service_solutions', [
                    'label' => false,
                ])
                    ->add('serviceSolutions', CollectionType::class, [
                        'label' => false,
                        'type_options' => [
                            'delete' => true,
                        ],
                        'by_reference' => false,
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'ba_custom_hide_fields' => ['solution'],
                    ])
                ->end()
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('serviceProvider',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service.serviceSystem',
            null,
            [
                'label' => 'app.service.entity.service_system',
                'admin_code' => ServiceSystemAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service.serviceSystem.jurisdictions',
            null,
            ['label' => 'app.service_system.entity.jurisdictions'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service.serviceSystem.situation.subject',
            null,
            ['label' => 'app.situation.entity.subject'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('maturity',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service',
            null,
            [
                'label' => 'app.service_solution.entity.service',
                'admin_code' => \App\Admin\ServiceAdmin::class
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status');
        $datagridMapper->add('portals',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $this->addCommunesDatagridFilters($datagridMapper);
        $datagridMapper->add('specializedProcedures',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('formServers',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('paymentTypes',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('authentications',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('analogServices',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('openDataItems',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('name');
        $datagridMapper->add('description');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('communes', null, [
                'template' => 'SolutionAdmin/list_communes.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'communes'],
                ]
            ])/*
            ->add('portals', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'portals'],
                ]
            ])*/
            ->add('serviceProvider', null, [
                'template' => 'General/Association/list_many_to_one_nolinks.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceProvider'],
                ]
            ])
            ->add('serviceSystems', null, [
                'admin_code' => ServiceSystemAdmin::class,
                //'associated_property' => 'name',
                'template' => 'SolutionAdmin/list-service-systems.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystems'],
                ]
            ])
            ->add('jurisdictions', 'string', [
                'label' => 'app.service_system.entity.jurisdictions',
                //'associated_property' => 'name',
                'template' => 'SolutionAdmin/list-jurisdiction.html.twig',
            ])
            ->add('name')/*
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])*/
            ->add('maturity', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'maturity'],
                ]
            ])
            ->add('url', 'url', [
                'required' => false
            ]);
        $this->addDefaultListActions($listMapper);
    }

    public function getExportFields()
    {
        $fields = parent::getExportFields();
        $additionalFields = [
            // 'communes', 'serviceSystems', 'serviceSystems.jurisdictions',
            'serviceProvider', 'customProvider', 'name', 'maturity', 'url', 'status',
        ];
        foreach ($additionalFields as $field) {
            if (!in_array($field, $fields, false)) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('communes', 'choice', [
                'associated_property' => 'name',
                'template' => 'SolutionAdmin/show-communes.html.twig',
            ])
            ->add('serviceProvider')
            ->add('customProvider')
            ->add('portals')
            ->add('specializedProcedures')
            ->add('formServers')
            ->add('paymentTypes')
            ->add('authentications')
            ->add('analogServices')
            ->add('openDataItems')
            ->add('name')
            ->add('description')
            ->add('url', 'url')
            ->add('contact')
            ->add('solutionContacts');

        $this->addServiceSystemsShowFields($showMapper);
        $showMapper
            ->add('serviceSolutions', null, [
                'associated_property' => 'service'
            ])
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
    }
}
