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

use App\Admin\Traits\MinistryStateTrait;
use App\Admin\Traits\ServiceTrait;
use App\Admin\Traits\SolutionTrait;
use App\Entity\Jurisdiction;
use App\Entity\ServiceSystem;
use App\Entity\Status;
use App\Form\DataTransformer\EntityCollectionToIdArrayTransformer;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceSystemAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use MinistryStateTrait;
    use ServiceTrait;
    use SolutionTrait;

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
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        //$this->addLaboratoriesFormFields($formMapper);
        $formMapper->add('jurisdictions', ChoiceFieldMaskType::class, [
            'label' => 'app.service_system.entity.jurisdictions_form',
            'choices' => [
                'app.jurisdiction.entity.types.country' => Jurisdiction::TYPE_COUNTRY,
                'app.jurisdiction.entity.types.state' => Jurisdiction::TYPE_STATE,
                'app.jurisdiction.entity.types.commune' => Jurisdiction::TYPE_COMMUNE,
            ],
            'multiple' => true,
            'map' => [
                Jurisdiction::TYPE_COUNTRY => [],
                Jurisdiction::TYPE_STATE => ['stateMinistries', 'bureaus'],
                Jurisdiction::TYPE_COMMUNE => ['bureaus'],
            ],
            'required' => true,
        ]);
        $formMapper->get('jurisdictions')->addModelTransformer(new EntityCollectionToIdArrayTransformer(
            $this->getModelManager(),
            Jurisdiction::class
        ));
        $this->addStateMinistriesFormFields($formMapper);
        $formMapper
            ->add('bureaus', ModelType::class, [
                'label' => 'app.service_system.entity.bureaus_form',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
            ]);
        $formMapper->add('ruleAuthorities', ChoiceFieldMaskType::class, [
            'label' => 'app.service_system.entity.rule_authorities_form',
            'choices' => [
                'app.jurisdiction.entity.types.country' => Jurisdiction::TYPE_COUNTRY,
                'app.jurisdiction.entity.types.state' => Jurisdiction::TYPE_STATE,
                'app.jurisdiction.entity.types.commune' => Jurisdiction::TYPE_COMMUNE,
            ],
            'multiple' => true,
            'map' => [
                Jurisdiction::TYPE_COUNTRY => [],
                Jurisdiction::TYPE_STATE => ['authorityStateMinistries', 'authorityBureaus'],
                Jurisdiction::TYPE_COMMUNE => ['authorityBureaus'],
            ],
            'required' => true,
        ]);
        $formMapper->get('ruleAuthorities')->addModelTransformer(new EntityCollectionToIdArrayTransformer(
            $this->getModelManager(),
            Jurisdiction::class
        ));
        $formMapper
            ->add('authorityStateMinistries', ModelType::class, [
                'label' => 'app.service_system.entity.authority_state_ministries',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
            ])
            ->add('authorityBureaus', ModelType::class, [
                'label' => 'app.service_system.entity.authority_bureaus_form',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
            ]);
        $formMapper->end()
            ->end();
        $formMapper->tab('app.service_system.tabs.services')
            ->with('app.service_system.entity.services', [
                'label' => false,
                'box_class' => 'box-tab',
                'translation_domain' => 'messages',
            ])
            /*->add('services', CollectionType::class, [
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
            ])*/
            ->add('services', ModelAutocompleteType::class, [
                'btn_add' => 'app.common.model_list_type.add',
                'property' => 'name',
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'btn_catalogue' => 'messages',
            ], [
                    'admin_code' => ServiceAdmin::class,
                ]
            )
            ->end()
            ->end();

        $formMapper->tab('app.service_system.tabs.solutions')
            ->with('app.service_system.entity.solutions', [
                'label' => false,
                'box_class' => 'box-tab',
                'translation_domain' => 'messages',
            ])
            ->add('solutions', ModelType::class,
                [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ],
                [
                    'admin_code' => SolutionAdmin::class,
                ]
            )
            ->end()
            ->end();
    }

    public function preUpdate($object)
    {
        /** @var ServiceSystem $object */
        $this->saveInheritedValues($object);
    }

    public function prePersist($object)
    {
        /** @var ServiceSystem $object */
        $this->saveInheritedValues($object);
    }

    private function saveInheritedValues(ServiceSystem $object)
    {
        // Save inherited properties in service entities
        $jurisdictions = $object->getJurisdictions();
        $bureaus = $object->getBureaus();
        $services = $object->getServices();
        foreach ($services as $service) {
            if ($service->isInheritJurisdictions()) {
                foreach ($jurisdictions as $jurisdiction) {
                    $service->addJurisdiction($jurisdiction);
                }
            }
            if ($service->isInheritBureaus()) {
                foreach ($bureaus as $bureau) {
                    $service->addBureau($bureau);
                }
            }
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('serviceKey');
        //$this->addLaboratoriesDatagridFilters($datagridMapper);
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
        $this->addSolutionsDatagridFilters($datagridMapper);
        $datagridMapper->add('bureaus',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
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
        $showMapper->add('bureaus');
        $showMapper->add('ruleAuthorities');
        $showMapper->add('authorityBureaus');
        $showMapper->add('authorityStateMinistries');
        $this->addServicesShowFields($showMapper);
        $this->addSolutionsShowFields($showMapper);
        //$this->addLaboratoriesShowFields($showMapper);
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
