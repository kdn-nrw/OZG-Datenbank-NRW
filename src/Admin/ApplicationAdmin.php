<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;

use App\Admin\Application\ApplicationInterfaceAdmin;
use App\Admin\Application\ApplicationModuleAdmin;
use App\Admin\StateGroup\ServiceProviderAdmin;
use App\Admin\Traits\ApplicationCategoryTrait;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ManufaturerTrait;
use App\Admin\Traits\ServiceProviderTrait;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ApplicationAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use ApplicationCategoryTrait;
    use CommuneTrait;
    use ManufaturerTrait;
    use ServiceProviderTrait;

    protected function configureTabMenu(ItemInterface $menu, $action, ?AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && $action !== 'edit') {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        if (null !== $admin) {
            $id = $admin->getRequest()->get('id');

            $menu->addChild('app.application.actions.show', [
                'uri' => $admin->generateUrl('show', ['id' => $id])
            ]);

            if ($this->isGranted('EDIT')) {
                $menu->addChild('app.application.actions.edit', [
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

    protected function configureFormGroups(FormMapper $formMapper)
    {
        $formMapper
            ->tab('tabs.general', [
                'label' => 'app.application.tabs.general',
            ])
            ->with('general', [
                'label' => false,
                'class' => 'col-md-12',
                'tab' => false,
            ])
            ->end()
            ->end();
        $formMapper
            ->tab('tabs.privacy', [
                'label' => 'app.application.tabs.privacy',
            ])
            ->with('privacy', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $formMapper
            ->tab('tabs.security', [
                'label' => 'app.application.tabs.security',
            ])
            ->with('security', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $formMapper
            ->tab('tabs.accessibility', [
                'label' => 'app.application.tabs.accessibility',
            ])
            ->with('accessibility', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $formMapper
            ->tab('tabs.archive', [
                'label' => 'app.application.tabs.archive',
            ])
            ->with('archive', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $formMapper
            ->tab('tabs.context', [
                'label' => 'app.application.tabs.context',
            ])
            ->with('context', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $formMapper
            ->tab('tabs.license', [
                'label' => 'app.application.tabs.license',
            ])
            ->with('license', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->configureFormGroups($formMapper);
        $formMapper
            ->tab('tabs.general')
            ->with('general');
        $formMapper
            ->add('name', TextType::class);
        $formMapper
            /*->add('inHouseDevelopment', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ])*/
            ->add('inHouseDevelopment', ChoiceFieldMaskType::class, [
                'choices' => [
                    'app.application.entity.in_house_development_choices.no' => false,
                    'app.application.entity.in_house_development_choices.yes' => true,
                ],
                'map' => [
                    false => ['manufacturers'],
                    true => [],
                ],
                'required' => false,
            ]);
        $this->addManufaturersFormFields($formMapper);

        $this->addApplicationCategoriesFormFields($formMapper);
        $formMapper
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $formMapper->add('applicationModules', CollectionType::class, [
            'label' => 'app.application.entity.application_modules',
            'type_options' => [
                'delete' => true,
            ],
            'by_reference' => false,
        ], [
            'edit' => 'inline',
            'inline' => 'natural',
            'ba_custom_exclude_fields' => ['application'],
        ]);
        $formMapper->add('applicationInterfaces', CollectionType::class, [
            'label' => 'app.application.entity.application_interfaces',
            'type_options' => [
                'delete' => true,
            ],
            'by_reference' => false,
        ], [
            'edit' => 'inline',
            'inline' => 'natural',
            'ba_custom_exclude_fields' => ['application'],
        ]);
        $this->addCommunesFormFields($formMapper);
        $this->addServiceProvidersFormFields($formMapper);
        $formMapper->end()
            ->end();// end tabs.general
        $formMapper
            ->tab('tabs.privacy')
            ->with('privacy');
        $formMapper
            ->add('privacy', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $formMapper->end()
            ->end();//end tabs.privacy
        $formMapper
            ->tab('tabs.security')
            ->with('security')
            ->end()
            ->end();// end tabs.security
        $formMapper
            ->tab('tabs.accessibility')
            ->with('accessibility');
        $formMapper
            ->add('accessibility', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $formMapper->end()
            ->end();// end tabs.accessibility
        $formMapper
            ->tab('tabs.archive')
            ->with('archive');
        $formMapper
            ->add('archive', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $formMapper
            ->end()
            ->end();// end tabs.archive
        $formMapper
            ->tab('tabs.context')
            ->with('context')
            ->end()
            ->end();
        $formMapper
            ->tab('tabs.license')
            ->with('license')
            ->end()
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addDefaultDatagridFilter($datagridMapper, 'manufacturers');
        $this->addDefaultDatagridFilter($datagridMapper, 'categories');
        $this->addDefaultDatagridFilter($datagridMapper, 'communes');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceProviders');
        $datagridMapper->add('inHouseDevelopment');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        // Name | Kategorie | Hersteller | Eigenentwicklung (Hier würde nur ein „Ja“ oder ein Haken erscheinen) | KDN Mitglied (nur der Kurzname) alle Felder sortierbar
        $listMapper
            ->addIdentifier('name');
        $listMapper
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
        //$this->addCommunesListFields($listMapper);
        $listMapper->add('inHouseDevelopment', null, [
            'editable' => true
        ]);
        $listMapper
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
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description');
        $this->addApplicationCategoriesShowFields($showMapper);
        $this->addManufaturersShowFields($showMapper);
        $this->addCommunesShowFields($showMapper);
        $this->addServiceProvidersShowFields($showMapper);
        $showMapper
            ->add('accessibility', 'html')
            ->add('privacy', 'html')
            ->add('archive', 'html')
            ->add('inHouseDevelopment');
    }
}
