<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;

use App\Admin\ModelRegion\ModelRegionProjectAdmin;
use App\Admin\Traits\LaboratoryTrait;
use App\Admin\Traits\MinistryStateTrait;
use App\Admin\Traits\PortalTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
use App\Entity\FederalInformationManagementType;
use App\Entity\Jurisdiction;
use App\Entity\Priority;
use App\Entity\Status;
use App\Exporter\Source\ServiceFimValueFormatter;
use App\Form\DataTransformer\EntityCollectionToIdArrayTransformer;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceAdmin extends AbstractAppAdmin implements ExtendedSearchAdminInterface, EnableFullTextSearchAdminInterface
{
    use LaboratoryTrait;
    use MinistryStateTrait;
    use PortalTrait;
    use SpecializedProcedureTrait;

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.service.entity.service_system_situation' => 'app.service_system.entity.situation',
        'app.service.entity.service_system_situation_subject' => 'app.situation.entity.subject',
        'app.service.entity.service_system_service_key' => 'app.service_system.entity.service_key',
        'app.service.entity.service_system_priority' => 'app.service_system.entity.priority',
        'app.service.entity.service_solutions_solution' => 'app.service.entity.service_solutions',
    ];

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('app.service.tabs.general', [
                'label' => 'app.service.tabs.general',
                'tab' => true,
            ]);
        $form->with('general', [
            'class' => 'col-xs-12 col-md-6',
            'label' => 'app.service.groups.general',
        ]);
        /*
        $form->with('app.service.groups.general', [
                'label' => false,
                'box_class' => 'box-tab',
            ]);*/
        $form->add('name', TextareaType::class, [
            'required' => true,
        ])
            ->add('serviceKey', TextType::class, [
                'required' => true,
            ]);

        if (!$this->isExcludedFormField('serviceSystem')) {
            $form->add('serviceSystem', ModelAutocompleteType::class, [
                'property' => 'name',
                'required' => true,
            ], [
                'admin_code' => ServiceSystemAdmin::class
            ]);
        }
        $form
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
                'choice_translation_domain' => false,
            ])
            ->add('serviceType', TextType::class, [
                'required' => true,
            ])
            ->add('priority', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ]);
        $form->end();
        $form->with('laws', [
            'class' => 'col-xs-12 col-md-6',
            'label' => 'app.service.groups.laws',
        ]);
        $form
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
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ])
            ->add('relevance2', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ]);
        $form->end();
        $form->with('relations', [
            'class' => 'col-xs-12',
            'label' => 'app.service.groups.relations',
        ]);

        $form
            ->add('inheritJurisdictions', ChoiceType::class, [
                'choices' => [
                    'app.service.entity.inherit_jurisdictions.no' => false,
                    'app.service.entity.inherit_jurisdictions.yes' => true,
                ],
                'multiple' => false,
                'required' => true,
            ])
            ->add('jurisdictions', ChoiceFieldMaskType::class, [
                'choices' => [
                    'app.jurisdiction.entity.types.country' => Jurisdiction::TYPE_COUNTRY,
                    'app.jurisdiction.entity.types.state' => Jurisdiction::TYPE_STATE,
                    'app.jurisdiction.entity.types.commune' => Jurisdiction::TYPE_COMMUNE,
                ],
                'multiple' => true,
                'map' => [
                    Jurisdiction::TYPE_COUNTRY => [],
                    Jurisdiction::TYPE_STATE => ['inheritStateMinistries', 'stateMinistries', 'inheritBureaus', 'bureaus'],
                    Jurisdiction::TYPE_COMMUNE => ['inheritBureaus', 'bureaus'],
                ],
                'required' => true,
            ]);
        $form->get('jurisdictions')->addModelTransformer(new EntityCollectionToIdArrayTransformer(
            $this->getModelManager(),
            Jurisdiction::class
        ));

        $form
            ->add('inheritStateMinistries', ChoiceType::class, [
                'choices' => [
                    'app.service.entity.inherit_state_ministries.no' => false,
                    'app.service.entity.inherit_state_ministries.yes' => true,
                ],
                'multiple' => false,
                'required' => true,
            ])
            ->add('stateMinistries', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ]
            )
            ->add('inheritBureaus', ChoiceType::class, [
                'choices' => [
                    'app.service.entity.inherit_bureaus.no' => false,
                    'app.service.entity.inherit_bureaus.yes' => true,
                ],
                'multiple' => false,
                'required' => true,
            ])
            ->add('bureaus', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ]
            );
        $form
            ->add('inheritRuleAuthorities', ChoiceType::class, [
                'choices' => [
                    'app.service.entity.inherit_rule_authorities.no' => false,
                    'app.service.entity.inherit_rule_authorities.yes' => true,
                ],
                'multiple' => false,
                'required' => true,
            ])
            ->add('ruleAuthorities', ChoiceFieldMaskType::class, [
                'choices' => [
                    'app.jurisdiction.entity.types.country' => Jurisdiction::TYPE_COUNTRY,
                    'app.jurisdiction.entity.types.state' => Jurisdiction::TYPE_STATE,
                    'app.jurisdiction.entity.types.commune' => Jurisdiction::TYPE_COMMUNE,
                ],
                'multiple' => true,
                'map' => [
                    Jurisdiction::TYPE_COUNTRY => [],
                    Jurisdiction::TYPE_STATE => ['authorityInheritStateMinistries', 'authorityStateMinistries', 'authorityInheritBureaus', 'authorityBureaus'],
                    Jurisdiction::TYPE_COMMUNE => ['authorityInheritBureaus', 'authorityBureaus'],
                ],
                'required' => true,
            ]);
        $form->get('ruleAuthorities')->addModelTransformer(new EntityCollectionToIdArrayTransformer(
            $this->getModelManager(),
            Jurisdiction::class
        ));

        $form
            ->add('authorityInheritStateMinistries', ChoiceType::class, [
                'choices' => [
                    'app.service.entity.authority_inherit_state_ministries.no' => false,
                    'app.service.entity.authority_inherit_state_ministries.yes' => true,
                ],
                'multiple' => false,
                'required' => true,
            ])
            ->add('authorityStateMinistries', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ]
            );

        $form
            ->add('authorityInheritBureaus', ChoiceType::class, [
                'choices' => [
                    'app.service.entity.authority_inherit_bureaus.no' => false,
                    'app.service.entity.authority_inherit_bureaus.yes' => true,
                ],
                'multiple' => false,
                'required' => true,
            ])
            ->add('authorityBureaus', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ]
            );

        $form
            ->add('inheritCommuneTypes', ChoiceType::class, [
                'choices' => [
                    'app.service.entity.inherit_commune_types.no' => false,
                    'app.service.entity.inherit_commune_types.yes' => true,
                ],
                'multiple' => false,
                'required' => true,
            ])
            ->add('communeTypes', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
            ]);
        $this->addSpecializedProceduresFormFields($form);
        $this->addPortalsFormFields($form);
        $form->end()
            ->end();
        if (!$this->isExcludedFormField('serviceSolutions')) {
            $form->tab('app.service.tabs.service_solutions', [
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
                    'ba_custom_exclude_fields' => ['service'],
                ])
                ->end()
                ->end();
        }
        $form->tab('app.service.tabs.fim', [
            'label' => 'app.service.tabs.fim',
            'box_class' => 'box-tab',
        ])
            ->with('app.service.entity.fim_types', [
                'label' => false,
                'class' => 'col-md-12 box-collection-static',
                'box_class' => 'box-tab',
            ])
            ->add('fimTypes', CollectionType::class, [
                'label' => false,
                'type_options' => [
                    'delete' => false,
                ],
                'btn_add' => false,
                'required' => true,
            ], [
                'admin_code' => FederalInformationManagementTypeAdmin::class,
                'edit' => 'inline',
                'inline' => 'natural',
                'ba_custom_exclude_fields' => ['service'],
            ])
            /*->add('fimTypes', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'entry_type' => FederalInformationManagementType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'block-fim-type',
                ],
            ])**/
            ->end()
            ->end();
        $form->tab('app.service.tabs.notes', [
            'label' => 'app.service.tabs.notes',
            'box_class' => 'box-tab',
        ])
            ->with('app.service.entity.notes', [
                'label' => false,
                'required' => false,
                'class' => 'col-md-12 box-group-notes',
                'box_class' => 'box-tab',
            ])
            ->add('notes', SimpleFormatterType::class, [
                'label' => 'app.service.entity.notes_placeholder',
                'format' => 'richhtml',
                'required' => false,
                'ckeditor_context' => 'default', // optional
            ]);
        $this->addLaboratoriesFormFields($form);
        $form->end()
            ->end();

    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $filter->add('serviceKey');
        $filter->add('serviceType');
        $this->addDefaultDatagridFilter($filter, 'serviceSystem');
        $this->addDefaultDatagridFilter($filter, 'priority');
        $filter->add('serviceSystem.serviceKey',
            null,
            ['label' => 'app.service_system.entity.service_key']
        );
        $this->addDefaultDatagridFilter($filter, 'serviceSystem.situation.subject', ['show_filter' => true]);
        $filter->add('status');
        $this->addDefaultDatagridFilter($filter, 'laboratories');
        $this->addDefaultDatagridFilter($filter, 'jurisdictions');
        $this->addDefaultDatagridFilter($filter, 'bureaus');
        $this->addDefaultDatagridFilter($filter, 'specializedProcedures');
        $this->addDefaultDatagridFilter($filter, 'portals');
        $this->addDefaultDatagridFilter($filter, 'implementationProjects.implementationProject');
        $this->addDefaultDatagridFilter($filter, 'ruleAuthorities');
        $this->addDefaultDatagridFilter($filter, 'stateMinistries');
        $this->addDefaultDatagridFilter($filter, 'serviceSolutions.solution');
        $filter->add('fimTypes.dataType',
            null, [
            ],
            ChoiceType::class,
            [
                'choices' => array_flip(FederalInformationManagementType::$mapTypes)
            ]
        );
        $filter->add('fimTypes.status',
            null, [
            ],
            ChoiceType::class,
            [
                'choices' => array_flip(FederalInformationManagementType::$statusChoices)
            ]
        );
        $this->addDefaultDatagridFilter($filter, 'communeTypes');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('serviceKey')
            ->add('serviceSystem', null, [
                'admin_code' => ServiceSystemAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystem'],
                ]
            ])
            ->add('serviceSystem.situation.subject', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystem'],
                    ['fieldName' => 'situation'],
                    ['fieldName' => 'subject'],
                ],
                'enable_filter_add' => true,
            ])
            ->add('serviceType')
            ->add('lawShortcuts')
            ->add('relevance1')
            ->add('relevance2')
            ->add('status', TemplateRegistryInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'status'],
                ]
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['fimTypes']);

        $additionalFields = [
            'serviceSystem.situation.subject', 'serviceSystem.situation', 'serviceSystem', 'serviceSystem.serviceKey',
            'name', 'serviceKey', 'serviceType', 'lawShortcuts', 'relevance1', 'relevance2', 'status'
        ];
        $customServiceFormatter = new ServiceFimValueFormatter();
        $fimStatusTypes = FederalInformationManagementType::$statusChoices;
        $statusTranslations = [];
        foreach ($fimStatusTypes as $status => $labelKey) {
            $statusTranslations[$status] = $this->trans($labelKey);
        }
        $customServiceFormatter->setFimStatusTranslations($statusTranslations);
        $fimTypes = FederalInformationManagementType::$mapTypes;
        foreach ($fimTypes as $type => $labelKey) {
            $typeField = ServiceFimValueFormatter::FIM_DATA_TYPE_PREFIX . $type;
            $statusField = ServiceFimValueFormatter::FIM_STATUS_PREFIX . $type;
            $settings->addCustomLabel($typeField, 'FIM Typ ' . $this->trans($labelKey));
            $settings->addCustomLabel($statusField, 'FIM Status ' . $this->trans($labelKey));
            $additionalFields[] = $typeField;
            $additionalFields[] = $statusField;
            $settings->addCustomPropertyValueFormatter($typeField, $customServiceFormatter);
            $settings->addCustomPropertyValueFormatter($statusField, $customServiceFormatter);
        }
        $settings->setAdditionFields($additionalFields);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('name', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('serviceKey', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('serviceSystem', null, [
                'admin_code' => ServiceSystemAdmin::class,
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
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('priority', FieldDescriptionInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => Priority::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.situation')
            ->add('serviceSystem.situation.subject')
            ->add('serviceSolutions', null, [
                'associated_property' => 'solution'
            ])
            ->add('jurisdictions')
            ->add('bureaus')
            ->add('ruleAuthorities')
            ->add('authorityBureaus')
            ->add('authorityStateMinistries')
            ->add('communeTypes')
            ->add('notes', FieldDescriptionInterface::TYPE_HTML);
        $this->addLaboratoriesShowFields($show);
        $this->addSpecializedProceduresShowFields($show);
        $this->addPortalsShowFields($show);
        $this->addStateMinistriesShowFields($show);
        $show->add('implementationProjects', null, [
            'admin_code' => ImplementationProjectAdmin::class,
            'template' => 'ServiceAdmin/Show/show-service-projects.html.twig',
            'is_custom_field' => true,
            'showProject' => true,
        ]);
        $show
            ->add('modelRegionProjects', null, [
                'admin_code' => ModelRegionProjectAdmin::class,
                'route' => [
                    'name' => 'edit',
                ],
            ])
            ->add('fimTypes');
    }
}
