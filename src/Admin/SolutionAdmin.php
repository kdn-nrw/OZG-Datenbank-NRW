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
use App\Admin\StateGroup\CommuneAdmin;
use App\Admin\StateGroup\CommuneSolutionAdmin;
use App\Admin\StateGroup\ServiceProviderAdmin;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\ModelRegionProjectTrait;
use App\Admin\Traits\PortalTrait;
use App\Admin\Traits\ServiceProviderTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Admin\Traits\SluggableTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
use App\DependencyInjection\InjectionTraits\InjectAuthorizationCheckerTrait;
use App\Entity\ConfidenceLevel;
use App\Entity\Solution;
use App\Entity\Status;
use App\Exporter\Source\ServiceSolutionValueFormatter;
use App\Model\ExportSettings;
use App\Service\SolutionHelper;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
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
    use SluggableTrait;
    use InjectAuthorizationCheckerTrait;

    /**
     * @var SolutionHelper
     */
    private $solutionHelper;

    /**
     * @required
     * @param SolutionHelper $solutionHelper
     */
    public function injectSolutionHelper(SolutionHelper $solutionHelper): void
    {
        $this->solutionHelper = $solutionHelper;
    }

    /**
     * Configures a list of default sort values.
     *
     * @phpstan-param array{_page?: int, _per_page?: int, _sort_by?: string, _sort_order?: string} $sortValues
     * @param array $sortValues
     */
    protected function configureDefaultSortValues(array &$sortValues)
    {
        parent::configureDefaultSortValues($sortValues);
        $sortValues[DatagridInterface::SORT_ORDER] = $sortValues[DatagridInterface::SORT_ORDER] ?? 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = $sortValues[DatagridInterface::SORT_BY] ?? 'id';
    }

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.solution.entity.form_server_solutions_form_server' => 'app.solution.entity.form_server_solutions',
    ];

    protected function configureTabMenu(ItemInterface $menu, $action, ?AdminInterface $childAdmin = null)
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

            if ($this->isGranted('LIST') && null !== $childAdmin = $admin->getChild(ServiceSolutionAdmin::class)) {
                $menu->addChild('app.solution.actions.list', [
                    'uri' => $childAdmin->generateUrl('list')
                ]);
            }
        }
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('app.solution.tabs.general', ['tab' => true])
            ->with('general', [
                'label' => false,
            ]);
        $this->addServiceProvidersFormFields($form);
        $form
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
        $this->addContactsFormFields($form, true, false, 'solutionContacts');
        $form->add('isPublished', CheckboxType::class, [
            'required' => false,
        ]);
        $this->addSlugFormField($form, $this->getSubject());
        $form->add('confidenceLevel', ModelType::class, [
            'btn_add' => false,
            'required' => true,
            'choice_translation_domain' => false,
        ]);
        $form
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
        $this->addPortalsFormFields($form);
        $form->add('enabledMunicipalPortal', CheckboxType::class, [
            'required' => false,
        ]);
        $overrideOptions = [];
        if (!$this->authorizationChecker->isGranted('ROLE_APP_SOLUTION_COMMUNE_EDIT', $this->getSubject())) {
            $overrideOptions = [
                'disabled' => true,
            ];
            $form
                ->add('communeType', ChoiceType::class, [
                    'choices' => [
                        'app.solution.entity.commune_type_all' => 'all',
                        'app.solution.entity.commune_type_selected' => 'selected',
                    ],
                    'disabled' => true,
                ]);
        } else {
            $form
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
        }
        $this->addCommunesFormFields($form, $overrideOptions);
        $this->addSpecializedProceduresFormFields($form);
        $form
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
        $this->addModelRegionProjectsFormFields($form);
        $form
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
                'ba_custom_exclude_fields' => ['solution'],
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
                'ba_custom_exclude_fields' => ['solution'],
            ])
            ->end()
            ->end();
    }

    public function postUpdate($object)
    {
        /** @var Solution $object */
        $this->solutionHelper->updateCommuneReferences($object);
    }

    public function postPersist($object)
    {
        /** @var Solution $object */
        $this->solutionHelper->updateCommuneReferences($object);
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $this->addDefaultDatagridFilter($filter, 'serviceProviders');
        $this->addDefaultDatagridFilter($filter, 'serviceSolutions.service.serviceSystem');
        $this->addDefaultDatagridFilter($filter, 'serviceSolutions.service.serviceSystem.jurisdictions');
        $this->addDefaultDatagridFilter($filter, 'serviceSolutions.service.serviceSystem.situation.subject');
        $this->addDefaultDatagridFilter($filter, 'maturity');
        $this->addDefaultDatagridFilter($filter, 'serviceSolutions.service');
        $filter->add('status');
        $this->addDefaultDatagridFilter($filter, 'portals');
        $filter->add('communeType', null,
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
        $this->addDefaultDatagridFilter($filter, 'communeSolutions.commune', [
            'label' => 'app.solution.entity.communes',
        ]);
        $this->addDefaultDatagridFilter($filter, 'specializedProcedures');
        $this->addDefaultDatagridFilter($filter, 'formServerSolutions.formServer');
        $this->addDefaultDatagridFilter($filter, 'paymentTypes');
        $this->addDefaultDatagridFilter($filter, 'authentications');
        $this->addDefaultDatagridFilter($filter, 'analogServices');
        $this->addDefaultDatagridFilter($filter, 'openDataItems');
        $filter->add('name');
        $filter->add('description');
        $filter->add('isPublished');
        $this->addDefaultDatagridFilter($filter, 'modelRegionProjects');
        $filter->add('confidenceLevel');
        $filter->add('enabledMunicipalPortal');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('selectedCommuneSolutions', null, [
                'label' => 'app.solution.entity.communes',
                'admin_code' => CommuneAdmin::class,
                'template' => 'SolutionAdmin/list_communes.html.twig',
                'associated_property' => 'commune',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'communeSolutions'],
                    ['fieldName' => 'commune'],
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
                'enable_filter_add' => true,
            ])
            ->add('name')/*
            ->add('status', TemplateRegistryInterface::TYPE_CHOICE, [
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
                ],
                'enable_filter_add' => true,
            ])
            ->add('url', 'url');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $customServiceFormatter = new ServiceSolutionValueFormatter();
        $customServiceFormatter->setDisplayType(ServiceSolutionValueFormatter::DISPLAY_SERVICE_KEY);
        $settings->setAdditionFields([
            // 'communes', 'serviceSystems', 'serviceSystems.jurisdictions', 'serviceProvider',
            'customProvider', 'name', 'maturity', 'url', 'status',
        ]);
        $settings->addCustomPropertyValueFormatter('serviceSolutions', $customServiceFormatter);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('name')
            ->add('description')
            ->add('url', 'url')
            ->add('paymentTypes')
            ->add('authentications')
            ->add('analogServices')
            ->add('openDataItems')
            ->add('contact')
            ->add('solutionContacts');

        $show
            ->add('status', TemplateRegistryInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('confidenceLevel', TemplateRegistryInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => ConfidenceLevel::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
        $this->addPortalsShowFields($show);
        $this->addSpecializedProceduresShowFields($show);
        $show
            ->add('customProvider');
        $enableTabs = true;
        // Tab fields
        $show
            ->add('connectionPlannedCommuneSolutions', null, [
                'label' => 'app.solution.entity.connection_planned_commune_solutions',
                'admin_code' => CommuneSolutionAdmin::class,
                'associated_property' => 'name',
                'custom_label' => 'app.solution.entity.connection_planned_commune_solutions',
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
                'referenceProperty' => 'communeSolutions',
                'show_export' => true,
            ])
            ->add('serviceProviders', null, [
                'admin_code' => ServiceProviderAdmin::class,
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ])
            ->add('formServerSolutions', null, [
                'associated_property' => 'formServer',
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ])
            ->add('serviceSystems', null,[
                'admin_code' => ServiceSystemAdmin::class,
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ])
            ->add('serviceSolutions', null, [
                'associated_property' => 'service',
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ])
            ->add('modelRegionProjects', null,[
                'admin_code' => ModelRegionProjectAdmin::class,
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ]);
    }
}
