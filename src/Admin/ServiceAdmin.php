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

use App\Admin\Traits\ImplementationProjectTrait;
use App\Admin\Traits\LaboratoryTrait;
use App\Admin\Traits\MinistryStateTrait;
use App\Admin\Traits\PortalTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
use App\Entity\Jurisdiction;
use App\Entity\Priority;
use App\Entity\Status;
use App\Exporter\Source\ServiceFimValueFormatter;
use App\Form\DataTransformer\EntityCollectionToIdArrayTransformer;
use App\Form\Type\FederalInformationManagementType;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
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
    use ImplementationProjectTrait;

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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $hideFields = $this->getFormHideFields();
        $formMapper
            ->with('app.service.tabs.general', [
                'label' => 'app.service.tabs.general',
                'tab' => true,
            ]);
        $formMapper->with('general', [
            'class' => 'col-xs-12 col-md-6',
            'label' => 'app.service.groups.general',
        ]);
        /*
        $formMapper->with('app.service.groups.general', [
                'label' => false,
                'box_class' => 'box-tab',
            ]);*/
        $formMapper->add('name', TextareaType::class, [
                'required' => true,
            ])
            ->add('serviceKey', TextType::class, [
                'required' => true,
            ]);

        if (!in_array('serviceSystem', $hideFields, false)) {
            $formMapper->add('serviceSystem', ModelAutocompleteType::class, [
                'property' => 'name',
                'required' => true,
            ], [
                'admin_code' => ServiceSystemAdmin::class
            ]);
        }
        $formMapper
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
                'choice_translation_domain' => false,
            ])
            ->add('serviceType', TextType::class, [
                'required' => true,
            ]);
        $formMapper->end();
        $formMapper->with('laws', [
            'class' => 'col-xs-12 col-md-6',
            'label' => 'app.service.groups.laws',
        ]);
        $formMapper
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
        $formMapper->end();
        $formMapper->with('relations', [
            'class' => 'col-xs-12',
            'label' => 'app.service.groups.relations',
        ]);

        $formMapper
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
        $formMapper->get('jurisdictions')->addModelTransformer(new EntityCollectionToIdArrayTransformer(
            $this->getModelManager(),
            Jurisdiction::class
        ));

        $formMapper
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
        $formMapper
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
        $formMapper->get('ruleAuthorities')->addModelTransformer(new EntityCollectionToIdArrayTransformer(
            $this->getModelManager(),
            Jurisdiction::class
        ));

        $formMapper
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

        $formMapper
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

        $formMapper
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
        $this->addSpecializedProceduresFormFields($formMapper);
        $this->addPortalsFormFields($formMapper);
        $formMapper->end()
            ->end();
        if (!in_array('serviceSolutions', $hideFields, false)) {
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
        $formMapper->tab('app.service.tabs.fim', [
            'label' => 'app.service.tabs.fim',
            'box_class' => 'box-tab',
        ])
            ->with('app.service.entity.fim_types', [
                'label' => false,
                'class' => 'col-md-12 box-group-service-fim',
                'box_class' => 'box-tab',
            ])
            ->add('fimTypes', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'entry_type' => FederalInformationManagementType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'block-fim-type',
                ],
            ])
            ->end()
            ->end();
        $formMapper->tab('app.service.tabs.notes', [
            'label' => 'app.service.tabs.notes',
            'box_class' => 'box-tab',
        ])
            ->with('app.service.entity.notes', [
                'label' => false,
                'class' => 'col-md-12 box-group-notes',
                'box_class' => 'box-tab',
            ])
            ->add('notes', SimpleFormatterType::class, [
                'label' => 'app.service.entity.notes_placeholder',
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $this->addLaboratoriesFormFields($formMapper);
        $formMapper->end()
            ->end();

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('serviceKey');
        $datagridMapper->add('serviceType');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSystem');
        $datagridMapper->add('serviceSystem.serviceKey',
            null,
            ['label' => 'app.service_system.entity.service_key']
        );
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSystem.situation.subject', ['show_filter' => true]);
        $datagridMapper->add('status');
        $this->addDefaultDatagridFilter($datagridMapper, 'laboratories');
        $this->addDefaultDatagridFilter($datagridMapper, 'jurisdictions');
        $this->addDefaultDatagridFilter($datagridMapper, 'bureaus');
        $this->addDefaultDatagridFilter($datagridMapper, 'specializedProcedures');
        $this->addDefaultDatagridFilter($datagridMapper, 'portals');
        $this->addDefaultDatagridFilter($datagridMapper, 'implementationProjects');
        $this->addDefaultDatagridFilter($datagridMapper, 'ruleAuthorities');
        $this->addDefaultDatagridFilter($datagridMapper, 'stateMinistries');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSolutions.solution');
        $datagridMapper->add('fimTypes.dataType',
            null, [
            ],
            ChoiceType::class,
            [
                'choices' => array_flip(\App\Entity\FederalInformationManagementType::$mapTypes)
            ]
        );
        $datagridMapper->add('fimTypes.status',
            null, [
            ],
            ChoiceType::class,
            [
                'choices' => array_flip(\App\Entity\FederalInformationManagementType::$statusChoices)
            ]
        );
        $this->addDefaultDatagridFilter($datagridMapper, 'communeTypes');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
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
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'status'],
                ]
            ]);
        $this->addDefaultListActions($listMapper);
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
        $fimStatusTypes = \App\Entity\FederalInformationManagementType::$statusChoices;
        $statusTranslations = [];
        foreach ($fimStatusTypes as $status => $labelKey) {
            $statusTranslations[$status] = $this->trans($labelKey);
        }
        $customServiceFormatter->setFimStatusTranslations($statusTranslations);
        $fimTypes = \App\Entity\FederalInformationManagementType::$mapTypes;
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
            ->add('status', TemplateRegistry::TYPE_CHOICE, [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.priority', TemplateRegistry::TYPE_CHOICE, [
                'label' => 'app.service_system.entity.priority',
                'editable' => true,
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
            ->add('notes', 'html');
        $this->addLaboratoriesShowFields($showMapper);
        $this->addSpecializedProceduresShowFields($showMapper);
        $this->addPortalsShowFields($showMapper);
        $this->addStateMinistriesShowFields($showMapper);
        $this->addImplementationProjectsShowFields($showMapper);
        $showMapper
            ->add('modelRegionProjects', null, [
                'admin_code' => ModelRegionProjectAdmin::class,
                'route' => [
                    'name' => 'edit',
                ],
            ])
            ->add('fimTypes');
    }
}
