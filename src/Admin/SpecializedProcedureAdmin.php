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

use App\Admin\Application\ApplicationCategoryAdmin;
use App\Admin\Application\ApplicationInterfaceAdmin;
use App\Admin\Application\ApplicationModuleAdmin;
use App\Admin\StateGroup\ServiceProviderAdmin;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ManufaturerTrait;
use App\Admin\Traits\OrganisationTrait;
use App\Admin\Traits\ServiceProviderTrait;
use App\Admin\Traits\ServiceTrait;
use App\Entity\Application\ApplicationAccessibilityDocument;
use App\Entity\Manufacturer;
use App\Entity\SpecializedProcedure;
use App\Entity\StateGroup\ServiceProvider;
use App\Form\Type\ApplicationAccessibilityDocumentType;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Sonata\Form\Type\CollectionType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SpecializedProcedureAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use CommuneTrait;
    use ManufaturerTrait;
    use OrganisationTrait;
    use ServiceTrait;
    use ServiceProviderTrait;

    protected function configureTabMenu(ItemInterface $menu, $action, ?AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && $action !== 'edit') {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        if (null !== $admin) {
            $id = $admin->getRequest()->get('id');

            $menu->addChild('app.specialized_procedure.actions.show', [
                'uri' => $admin->generateUrl('show', ['id' => $id])
            ]);

            if ($this->isGranted('EDIT')) {
                $menu->addChild('app.specialized_procedure.actions.edit', [
                    'uri' => $admin->generateUrl('edit', ['id' => $id])
                ]);
            }

            if ($this->isGranted('LIST') && null !== $childAdmin = $admin->getChild(ApplicationModuleAdmin::class)) {
                $menu->addChild('app.application_module.breadcrumb.link_application_module_list', [
                    'uri' => $childAdmin->generateUrl('list')
                ]);
            }

            if ($this->isGranted('LIST') && null !== $childAdmin = $admin->getChild(ApplicationInterfaceAdmin::class)) {
                $menu->addChild('app.application_interface.breadcrumb.link_application_interface_list', [
                    'uri' => $childAdmin->generateUrl('list')
                ]);
            }
        }
    }

    protected function configureFormGroups(FormMapper $form): void
    {
        $form
            ->tab('tabs.general', [
                'label' => 'app.specialized_procedure.tabs.general',
            ])
            ->with('general', [
                'label' => false,
                'class' => 'col-md-12',
                'tab' => false,
            ])
            ->end()
            ->end();
        $form
            ->tab('tabs.privacy', [
                'label' => 'app.specialized_procedure.tabs.privacy',
            ])
            ->with('privacy', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $form
            ->tab('tabs.security', [
                'label' => 'app.specialized_procedure.tabs.security',
            ])
            ->with('security', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $form
            ->tab('tabs.accessibility', [
                'label' => 'app.specialized_procedure.tabs.accessibility',
            ])
            ->with('accessibility_general', [
                'label' => false,
                'class' => 'col-xs-12 col-lg-6',
            ])
            ->end()
            ->with('accessibility_meta', [
                'label' => false,
                'class' => 'col-xs-12 col-lg-6',
            ])
            ->end()
            ->with('accessibility_documents', [
                'label' => 'app.specialized_procedure.groups.accessibility_documents',
                'class' => 'clear-left-md col-xs-12 col-lg-6',
            ])
            ->end()
            ->end();
        $form
            ->tab('tabs.archive', [
                'label' => 'app.specialized_procedure.tabs.archive',
            ])
            ->with('archive', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $form
            ->tab('tabs.context', [
                'label' => 'app.specialized_procedure.tabs.context',
            ])
            ->with('context', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $form
            ->tab('tabs.license', [
                'label' => 'app.specialized_procedure.tabs.license',
            ])
            ->with('license', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $this->configureFormGroups($form);
        $form
            ->tab('tabs.general')
            ->with('general');
        $form
            ->add('name', TextType::class);
        $form
            ->add('inHouseDevelopment', ChoiceFieldMaskType::class, [
                'choices' => array_flip(SpecializedProcedure::$inHouseDevelopmentChoices),
                'map' => [
                    SpecializedProcedure::IN_HOUSE_DEVELOPMENT_NO => ['manufacturers'],
                    SpecializedProcedure::IN_HOUSE_DEVELOPMENT_YES => [],
                    SpecializedProcedure::IN_HOUSE_DEVELOPMENT_YES_REUSE => [],
                ],
                'required' => false,
            ]);
        $this->addManufaturersFormFields($form);
        $this->addServicesFormFields($form);
        $form
            ->add('categories', ModelAutocompleteType::class, [
                'property' => 'name',
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
            ],
                [
                    'admin_code' => ApplicationCategoryAdmin::class,
                ]
            );
        $form
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $form->add('applicationModules', CollectionType::class, [
            'label' => 'app.specialized_procedure.entity.application_modules',
            'type_options' => [
                'delete' => true,
            ],
            'by_reference' => false,
        ], [
            'edit' => 'inline',
            'inline' => 'natural',
            'ba_custom_exclude_fields' => ['application'],
        ]);
        $form->add('applicationInterfaces', CollectionType::class, [
            'label' => 'app.specialized_procedure.entity.application_interfaces',
            'type_options' => [
                'delete' => true,
            ],
            'by_reference' => false,
        ], [
            'edit' => 'inline',
            'inline' => 'natural',
            'ba_custom_exclude_fields' => ['application'],
        ]);
        $this->addCommunesFormFields($form);
        $this->addServiceProvidersFormFields($form);
        $filterChoices = [
            'entityTypes' => [
                ServiceProvider::class,
                Manufacturer::class,
            ],
        ];
        $this->addOrganisationsFormFields($form, 'businessPremises', $filterChoices);
        $form->end()
            ->end();// end tabs.general
        $form
            ->tab('tabs.privacy')
            ->with('privacy');
        $form
            ->add('privacy', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $form->end()
            ->end();//end tabs.privacy
        $form
            ->tab('tabs.security')
            ->with('security')
            ->end()
            ->end();// end tabs.security
        $form
            ->tab('tabs.accessibility')
            ->with('accessibility_general');
        $form
            ->add('accessibilityTestConducted', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $form->end();
        $form->with('accessibility_meta');
        $filterChoices = [
            'entityTypes' => [
                ServiceProvider::class,
            ],
        ];
        $this->addOrganisationsFormFields($form, 'accessibilityTestOrganisations', $filterChoices);
        $form
            ->add('accessibilityTestOrganisationOthers', TextareaType::class, [
                'label' => 'app.specialized_procedure.entity.accessibility_test_organisation_others_form',
                'required' => false,
            ])
            ->add('accessibilitySelfTesting', ChoiceType::class, [
                'choices' => [
                    'app.specialized_procedure.entity.accessibility_self_testing_choices.no' => false,
                    'app.specialized_procedure.entity.accessibility_self_testing_choices.yes' => true,
                ],
                'multiple' => false,
                'required' => true,
            ])
            ->add('accessibilityTestResultType', ChoiceType::class, [
                'choices' => array_flip(SpecializedProcedure::ACCESSIBILITY_TEST_RESULT_TYPES),
                'multiple' => false,
                'required' => true,
            ]);
        $form->end();
        $form->with('accessibility_documents');
        $form->add('accessibilityDocuments', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
            'label' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'entry_type' => ApplicationAccessibilityDocumentType::class,
            'entry_options' => [
                'parent_admin' => $this,
            ],
        ]);
        $form->end()
            ->end();// end tabs.accessibility
        $form
            ->tab('tabs.archive')
            ->with('archive');
        $form
            ->add('archive', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $form
            ->end()
            ->end();// end tabs.archive
        $form
            ->tab('tabs.context')
            ->with('context')
            ->end()
            ->end();
        $form
            ->tab('tabs.license')
            ->with('license')
            ->end()
            ->end();
    }

    public function preUpdate($object): void
    {
        $this->cleanDocuments($object);
    }

    public function prePersist($object): void
    {
        $this->cleanDocuments($object);
    }

    public function cleanDocuments($object)
    {
        /** @var SpecializedProcedure $object */
        $removeDocuments = $object->cleanAccessibilityDocuments();

        if (!empty($removeDocuments)) {
            /** @var ModelManager $modelManager */
            $modelManager = $this->getModelManager();
            $docEm = $modelManager->getEntityManager(ApplicationAccessibilityDocument::class);
            foreach ($removeDocuments as $document) {
                if ($docEm->contains($document)) {
                    $docEm->remove($document);
                }
            }
        }
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'manufacturers');
        $this->addDefaultDatagridFilter($filter, 'services');
        $this->addDefaultDatagridFilter($filter, 'categories');
        $this->addDefaultDatagridFilter($filter, 'communes');
        $this->addDefaultDatagridFilter($filter, 'serviceProviders');
        $this->addDefaultDatagridFilter($filter, 'businessPremises');
        $this->addDefaultDatagridFilter($filter, 'accessibilityTestOrganisations');
        $this->addDefaultDatagridFilter($filter, 'accessibilitySelfTesting');
        $filter
            ->add('inHouseDevelopment', ChoiceFilter::class, [
                'label' => 'app.specialized_procedure.entity.in_house_development',
                'field_options' => [
                    'choices' => array_flip(SpecializedProcedure::$inHouseDevelopmentChoices),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    //'choice_translation_domain' => 'SonataAdminBundle',
                ],
                'field_type' => ChoiceType::class,
            ])
            ->add('accessibilityTestResultType', ChoiceFilter::class, [
                'label' => 'app.specialized_procedure.entity.accessibility_test_result_type',
                'field_options' => [
                    'choices' => SpecializedProcedure::ACCESSIBILITY_TEST_RESULT_TYPES,
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    //'choice_translation_domain' => 'SonataAdminBundle',
                ],
                'field_type' => ChoiceType::class,
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        // Name | Kategorie | Hersteller | Eigenentwicklung (Hier würde nur ein „Ja“ oder ein Haken erscheinen) | KDN Mitglied (nur der Kurzname) alle Felder sortierbar
        $list
            ->addIdentifier('name');
        $list
            ->add('categories', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'categories'],
                ],
                'enable_filter_add' => true,
            ])
            ->add('manufacturers', null, [
                'admin_code' => ManufacturerAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'manufacturers'],
                ],
                'enable_filter_add' => true,
            ]);
        //$this->addCommunesListFields($list);
        $list->add('inHouseDevelopment', 'choice', [
            'label' => 'app.specialized_procedure.entity.in_house_development',
            'editable' => true,
            'choices' => SpecializedProcedure::$inHouseDevelopmentChoices,
            'catalogue' => 'messages',
        ]);
        $list
            ->add('serviceProviders', null, [
                'admin_code' => ServiceProviderAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceProviders'],
                ],
                'enable_filter_add' => true,
            ]);
        $list
            ->add('references', 'string', [
                'label' => 'app.specialized_procedure.entity.commune_count',
                'template' => 'SpecializedProcedureAdmin/list-references.html.twig',
                'filterParamName' => 'specializedProcedures',
                'referenceLabel' => 'app.commune.type_label',
                //https://ozg.ddev.site/admin/state/commune/list?filter%5BfullText%5D%5Btype%5D=&filter%5BfullText%5D%5Bvalue%5D=&filter%5Borganisation__contacts%5D%5Btype%5D=&filter%5Borganisation__zipCode%5D%5Btype%5D=&filter%5Borganisation__zipCode%5D%5Bvalue%5D=&filter%5Borganisation__town%5D%5Btype%5D=&filter%5Borganisation__town%5D%5Bvalue%5D=&filter%5BserviceProviders%5D%5Btype%5D=&filter%5BcentralAssociations%5D%5Btype%5D=&filter%5BspecializedProcedures%5D%5Btype%5D=&filter%5BspecializedProcedures%5D%5Bvalue%5D=11&filter%5B_page%5D=1&filter%5B_sort_by%5D=name&filter%5B_sort_order%5D=ASC
                //https://ozg.ddev.site/admin/state/commune/list?filter%5BfullText%5D%5Btype%5D=&filter%5BfullText%5D%5Bvalue%5D=&filter%5Borganisation__contacts%5D%5Btype%5D=&filter%5Borganisation__zipCode%5D%5Btype%5D=&filter%5Borganisation__zipCode%5D%5Bvalue%5D=&filter%5Borganisation__town%5D%5Btype%5D=&filter%5Borganisation__town%5D%5Bvalue%5D=&filter%5BserviceProviders%5D%5Btype%5D=&filter%5BcentralAssociations%5D%5Btype%5D=&filter%5BspecializedProcedures%5D%5Btype%5D=&filter%5BspecializedProcedures%5D%5Bvalue%5D%5B%5D=7&filter%5Bportals%5D%5Btype%5D=&filter%5BpaymentPlatforms%5D%5Btype%5D=&filter%5Blaboratories%5D%5Btype%5D=&filter%5Bconstituency%5D%5Btype%5D=&filter%5BadministrativeDistrict%5D%5Btype%5D=&filter%5BcommuneType%5D%5Btype%5D=&filter%5BofficialCommunityKey%5D%5Btype%5D=&filter%5BofficialCommunityKey%5D%5Bvalue%5D=&filter%5BregionalKey%5D%5Btype%5D=&filter%5BregionalKey%5D%5Bvalue%5D=&filter%5Bname%5D%5Btype%5D=&filter%5Bname%5D%5Bvalue%5D=&filter%5B_page%5D=1&filter%5B_sort_by%5D=sorting&filter%5B_sort_order%5D=ASC&filter%5B_per_page%5D=32
                'route' => [
                    'prefix' => 'admin_app_stategroup_commune',
                    'name' => 'list',
                ],
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name')
            ->add('description');
        $this->addManufaturersShowFields($show);
        $show
            ->add('categories', null, [
                'admin_code' => ApplicationCategoryAdmin::class,
                //'template' => 'General/Show/show-categories.twig',
            ]);
        // applicationModules
        // applicationInterfaces
        $this->addServicesShowFields($show);
        $this->addCommunesShowFields($show);
        $this->addServiceProvidersShowFields($show);
        $this->addOrganisationsShowFields($show, 'businessPremises');
        $show
            ->add('privacy', FieldDescriptionInterface::TYPE_HTML);
        $show
            ->add('accessibilityTestConducted', FieldDescriptionInterface::TYPE_HTML);
        $this->addOrganisationsShowFields($show, 'accessibilityTestOrganisations');
        $show
            ->add('accessibilityTestOrganisationOthers', null, [
                'label' => 'app.specialized_procedure.entity.accessibility_test_organisation_others_form',
            ])
            ->add('accessibilitySelfTesting', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => [
                    false => 'app.specialized_procedure.entity.accessibility_self_testing_choices.no',
                    true => 'app.specialized_procedure.entity.accessibility_self_testing_choices.yes',
                ],
                'catalogue' => 'messages',
            ])
            ->add('accessibilityTestResultType', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => SpecializedProcedure::ACCESSIBILITY_TEST_RESULT_TYPES,
                'catalogue' => 'messages',
            ]);
        $show->add('accessibilityDocuments', null, [
            'template' => 'General/Show/show-attachments.html.twig',
        ]);
        $show
            ->add('archive', FieldDescriptionInterface::TYPE_HTML)
            ->add('inHouseDevelopment', FieldDescriptionInterface::TYPE_CHOICE, [
                'label' => 'app.specialized_procedure.entity.in_house_development',
                'editable' => true,
                'choices' => SpecializedProcedure::$inHouseDevelopmentChoices,
                'catalogue' => 'messages',
            ]);
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('download', $this->getRouterIdParameter() . '/download');
    }
}
