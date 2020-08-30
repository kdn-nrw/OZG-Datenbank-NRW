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

use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\ModelRegionProjectTrait;
use App\Admin\Traits\PortalTrait;
use App\Admin\Traits\ServiceProviderTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class SolutionAdmin extends AbstractAppAdmin implements ExtendedSearchAdminInterface, EnableFullTextSearchAdminInterface
{
    use CommuneTrait;
    use ContactTrait;
    use ModelRegionProjectTrait;
    use PortalTrait;
    use ServiceProviderTrait;
    use ServiceSystemTrait;
    use SpecializedProcedureTrait;

    protected $datagridValues = [

        // display the first page (default = 1)
        '_page' => 1,

        // reverse order (default = 'ASC')
        '_sort_order' => 'DESC',

        // name of the ordered field (default = the model's id field, if any)
        '_sort_by' => 'id',
    ];

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.solution.entity.form_server_solutions_form_server' => 'app.solution.entity.form_server_solutions',
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
            ]);
        $this->addServiceProvidersFormFields($formMapper);
        $formMapper
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
            ]);
        $this->addPortalsFormFields($formMapper);
        $formMapper
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
        $this->addSpecializedProceduresFormFields($formMapper);
        $formMapper
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
            ]);
        $this->addModelRegionProjectsFormFields($formMapper);
        $formMapper
            ->end()
            ->end()
            ->tab('app.solution.tabs.form_servers')
            ->with('form_server_solutions', [
                'label' => false,
            ])
            ->add('formServerSolutions', CollectionType::class, [
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
        $this->addServiceProvidersDatagridFilters($datagridMapper);
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
                'admin_code' => ServiceAdmin::class
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status');
        $this->addPortalsDatagridFilters($datagridMapper);
        $datagridMapper->add('communeType', null,
            [
            ],
            ChoiceType::class,
            [
                'choices' => [
                    'app.solution.entity.commune_type_all' => 'all',
                    'app.solution.entity.commune_type_selected' => 'selected',
                ],
                'expanded' => false,
                'multiple' => false,
            ]);
        $this->addCommunesDatagridFilters($datagridMapper);
        $this->addSpecializedProceduresDatagridFilters($datagridMapper);
        $datagridMapper->add('formServerSolutions.formServer',
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
        $datagridMapper->add('isPublished');
        $this->addModelRegionProjectsDatagridFilters($datagridMapper);
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
            ])
            ->add('serviceProviders', null, [
                'template' => 'SolutionAdmin/list-service-providers.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceProviders'],
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
                    ['fieldName' => 'serviceSolutions'],
                    ['fieldName' => 'service'],
                    ['fieldName' => 'serviceSystem'],
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
            ->add('url', 'url');
        $this->addDefaultListActions($listMapper);
    }

    public function getExportFields()
    {
        $fields = parent::getExportFields();
        $additionalFields = [
            // 'communes', 'serviceSystems', 'serviceSystems.jurisdictions', 'serviceProvider',
            'customProvider', 'name', 'maturity', 'url', 'status',
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
                'check_has_all_modifier' => true,
            ]);
        $this->addServiceProvidersShowFields($showMapper);
        $showMapper
            ->add('customProvider');
        $this->addPortalsShowFields($showMapper);
        $this->addSpecializedProceduresShowFields($showMapper);
        $showMapper
            ->add('formServerSolutions', null, [
                'associated_property' => 'formServer'
            ])
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
        $this->addModelRegionProjectsShowFields($showMapper);
    }
}
