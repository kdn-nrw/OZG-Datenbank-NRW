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

use App\Admin\Traits\LaboratoryTrait;
use App\Admin\Traits\MinistryStateTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
use App\Entity\Jurisdiction;
use App\Entity\Priority;
use App\Entity\Status;
use App\Form\DataTransformer\EntityCollectionToIdArrayTransformer;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use LaboratoryTrait;
    use SpecializedProcedureTrait;
    use MinistryStateTrait;

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

        if (!in_array('serviceSystem', $hideFields, false)) {
            $formMapper->add('serviceSystem', ModelAutocompleteType::class, [
                'property' => 'name',
                'required' => true,
            ], [
                'admin_code' => ServiceSystemAdmin::class
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
            )
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
        $this->addSpecializedProceduresFormFields($formMapper);
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
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('serviceKey');
        $datagridMapper->add('serviceType');
        $datagridMapper->add('serviceSystem',
            null, [
                'admin_code' => ServiceSystemAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSystem.serviceKey',
            null,
            ['label' => 'app.service_system.entity.service_key']
        );
        $datagridMapper->add('serviceSystem.situation.subject',
            null,
            [
                'show_filter' => true,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status');
        $this->addLaboratoriesDatagridFilters($datagridMapper);
        $datagridMapper->add('jurisdictions',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('bureaus',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $this->addSpecializedProceduresDatagridFilters($datagridMapper);
        $datagridMapper->add('ruleAuthorities',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $this->addStateMinistriesDatagridFilters($datagridMapper);
        $datagridMapper->add('serviceSolutions.solution',
            null, [
                'admin_code' => SolutionAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
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
            ]);
        $this->addDefaultListActions($listMapper);
    }

    public function getExportFields()
    {
        $fields = parent::getExportFields();
        $additionalFields = [
            'serviceSystem.situation.subject', 'serviceSystem.situation', 'serviceSystem', 'serviceSystem.serviceKey',
            'name', 'serviceKey', 'serviceType', 'lawShortcuts', 'relevance1', 'relevance2', 'status'
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
            ->add('serviceSystem.situation')
            ->add('serviceSystem.situation.subject')
            ->add('serviceSolutions')
            ->add('jurisdictions')
            ->add('bureaus')
            ->add('ruleAuthorities')
            ->add('authorityBureaus')
            ->add('authorityStateMinistries');
        $this->addLaboratoriesShowFields($showMapper);
        $this->addSpecializedProceduresShowFields($showMapper);
        $this->addStateMinistriesShowFields($showMapper);
    }
}
