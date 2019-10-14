<?php

namespace App\Admin;

use App\Entity\Status;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class SolutionAdmin extends AbstractAdmin
{
    protected $datagridValues = [

        // display the first page (default = 1)
        '_page' => 1,

        // reverse order (default = 'ASC')
        '_sort_order' => 'DESC',

        // name of the ordered field (default = the model's id field, if any)
        '_sort_by' => 'id',
    ];

    protected $labelGroup = 'app.entity.solution.';
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        $menu->addChild($this->labelGroup . 'actions.show', [
            'uri' => $admin->generateUrl('show', ['id' => $id])
        ]);

        if ($this->isGranted('EDIT')) {
            $menu->addChild($this->labelGroup . 'actions.edit', [
                'uri' => $admin->generateUrl('edit', ['id' => $id])
            ]);
        }

        if ($this->isGranted('LIST')) {
            $menu->addChild($this->labelGroup . 'actions.service_solution_list', [
                'uri' => $admin->getChild(ServiceSolutionAdmin::class)->generateUrl('list', ['id' => $id])
            ]);
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('serviceProvider', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'label' => $this->labelGroup . 'service_provider',
            ])
            ->add('customProvider', TextType::class, [
                'label' => $this->labelGroup . 'custom_provider',
                'required' => false,
            ])
            ->add('status', ModelType::class, [
                'label' => $this->labelGroup . 'status',
                'btn_add' => false,
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => $this->labelGroup . 'name',
                'required' => false,
            ])
            ->add('portals', ModelType::class, [
                'label' => $this->labelGroup . 'portals',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('communes', ModelType::class, [
                'label' => $this->labelGroup . 'communes',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('specializedProcedures', ModelType::class, [
                'label' => $this->labelGroup . 'specialized_procedures',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('formServers', ModelType::class, [
                'label' => $this->labelGroup . 'form_servers',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('paymentTypes', ModelType::class, [
                'label' => $this->labelGroup . 'payment_types',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('authentications', ModelType::class, [
                'label' => $this->labelGroup . 'authentications',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'app.entity.maturity.description',
                'required' => false,
            ])
            ->add('url', UrlType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'url'
            ])
            ->add('contact', TextareaType::class, [
                'label' => $this->labelGroup . 'contact',
                'required' => false,
            ])
            /*
            ->add('serviceSolutions', ModelType::class, [
                'label' => $this->labelGroup . 'service_solutions',
                'expanded' => true,
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])*/
            ->add('serviceSolutions', CollectionType::class, [
                'label' => $this->labelGroup . 'service_solutions',
                'type_options' => [
                    'delete' => true,
                ],
                'by_reference' => false,
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('serviceProvider',
            null,
            ['label' => $this->labelGroup . 'service_provider'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service.serviceSystem',
            null,
            ['label' => 'app.entity.service.service_system'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('maturity',
            null,
            ['label' => $this->labelGroup . 'maturity'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service',
            null,
            ['label' => $this->labelGroup . 'service_solutions'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status',
            null,
            ['label' => $this->labelGroup . 'status']
        );
        $datagridMapper->add('portals',
            null,
            ['label' => $this->labelGroup . 'portals'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('communes',
            null,
            ['label' => $this->labelGroup . 'communes'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('specializedProcedures',
            null,
            ['label' => $this->labelGroup . 'specialized_procedures'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('formServers',
            null,
            ['label' => $this->labelGroup . 'form_servers'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('paymentTypes',
            null,
            ['label' => $this->labelGroup . 'payment_types'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('authentications',
            null,
            ['label' => $this->labelGroup . 'authentications'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('name',
            null,
            ['label' => $this->labelGroup . 'name']
        );
        $datagridMapper->add('description',
            null,
            ['label' => $this->labelGroup . 'description']
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('communes', null, [
                'label' => $this->labelGroup . 'communes',
                'template' => 'General/Association/list_many_to_many_nolinks.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'communes'],
                ]
            ])/*
            ->add('portals', null, [
                'label' => $this->labelGroup . 'portals',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'portals'],
                ]
            ])*/
            ->add('serviceProvider', null, [
                'label' => $this->labelGroup . 'service_provider',
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
                'label' => $this->labelGroup . 'service_systems',
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
                'label' => 'app.entity.service_system.jurisdictions',
                //'associated_property' => 'name',
                'template' => 'SolutionAdmin/list-jurisdiction.html.twig',
            ])
            ->add('name', null, [
                'label' => $this->labelGroup . 'name',
            ])/*
            ->add('status', 'choice', [
                'label' => $this->labelGroup . 'status',
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])*/
            ->add('maturity', null, [
                'label' => $this->labelGroup . 'maturity',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'maturity'],
                ]
            ])
            ->add('url', 'url', [
                'required' => false,
                'label' => $this->labelGroup . 'url'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('communes', null, [
                'label' => $this->labelGroup . 'communes',
            ])
            ->add('serviceProvider', null, [
                'label' => $this->labelGroup . 'service_provider',
            ])
            ->add('portals', null, [
                'label' => $this->labelGroup . 'portals',
            ])
            ->add('specializedProcedures', null, [
                'label' => $this->labelGroup . 'specialized_procedures',
            ])
            ->add('formServers', null, [
                'label' => $this->labelGroup . 'form_servers',
            ])
            ->add('paymentTypes', null, [
                'label' => $this->labelGroup . 'payment_types',
            ])
            ->add('authentications', null, [
                'label' => $this->labelGroup . 'authentications',
            ])
            ->add('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('url', 'url', [
                'label' => $this->labelGroup . 'url',
            ])
            ->add('contact', null, [
                'label' => $this->labelGroup . 'contact',
            ])
            ->add('serviceSystems', 'choice', [
                'label' => $this->labelGroup . 'service_systems',
                'associated_property' => 'name',
                'template' => 'SolutionAdmin/show-service-systems.html.twig',
            ])
            ->add('serviceSolutions', null, [
                'label' => $this->labelGroup . 'service_solutions',
                'associated_property' => 'service'
            ])
            ->add('status', 'choice', [
                'label' => $this->labelGroup . 'status',
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
    }
}
