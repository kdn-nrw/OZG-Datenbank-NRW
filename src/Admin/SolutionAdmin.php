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

use App\Admin\StateGroup\CommuneAdmin;
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
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
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
        ]);
        $this->addSlugFormField($formMapper, $this->getSubject());
        $formMapper->add('confidenceLevel', ModelType::class, [
            'btn_add' => false,
            'required' => true,
            'choice_translation_domain' => false,
        ]);
        $formMapper
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
        $overrideOptions = [];
        if (!$this->authorizationChecker->isGranted('ROLE_APP_SOLUTION_COMMUNE_EDIT', $this->getSubject())) {
            $overrideOptions = [
                'disabled' => true,
            ];
            $formMapper
                ->add('communeType', ChoiceType::class, [
                    'choices' => [
                        'app.solution.entity.commune_type_all' => 'all',
                        'app.solution.entity.commune_type_selected' => 'selected',
                    ],
                    'disabled' => true,
                ]);
        } else {
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
        }
        $this->addCommunesFormFields($formMapper, $overrideOptions);
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceProviders');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSolutions.service.serviceSystem');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSolutions.service.serviceSystem.jurisdictions');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSolutions.service.serviceSystem.situation.subject');
        $this->addDefaultDatagridFilter($datagridMapper, 'maturity');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSolutions.service');
        $datagridMapper->add('status');
        $this->addDefaultDatagridFilter($datagridMapper, 'portals');
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
        $this->addDefaultDatagridFilter($datagridMapper, 'communeSolutions.commune', [
            'label' => 'app.solution.entity.communes',
        ]);
        $this->addDefaultDatagridFilter($datagridMapper, 'specializedProcedures');
        $this->addDefaultDatagridFilter($datagridMapper, 'formServerSolutions.formServer');
        $this->addDefaultDatagridFilter($datagridMapper, 'paymentTypes');
        $this->addDefaultDatagridFilter($datagridMapper, 'authentications');
        $this->addDefaultDatagridFilter($datagridMapper, 'analogServices');
        $this->addDefaultDatagridFilter($datagridMapper, 'openDataItems');
        $datagridMapper->add('name');
        $datagridMapper->add('description');
        $datagridMapper->add('isPublished');
        $this->addDefaultDatagridFilter($datagridMapper, 'modelRegionProjects');
        $datagridMapper->add('confidenceLevel');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('selectedCommuneSolutions', null, [
                'label' => 'app.solution.entity.communes',
                'admin_code' => CommuneAdmin::class,
                'template' => 'SolutionAdmin/list_communes.html.twig',
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
                ],
                'enable_filter_add' => true,
            ])
            ->add('url', 'url');
        $this->addDefaultListActions($listMapper);
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
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('communes', TemplateRegistry::TYPE_CHOICE, [
                'label' => 'app.solution.entity.communes',
                'admin_code' => CommuneAdmin::class,
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
            ->add('status', TemplateRegistry::TYPE_CHOICE, [
                //'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('confidenceLevel', TemplateRegistry::TYPE_CHOICE, [
                //'editable' => true,
                'class' => ConfidenceLevel::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
        $this->addModelRegionProjectsShowFields($showMapper);
    }
}
