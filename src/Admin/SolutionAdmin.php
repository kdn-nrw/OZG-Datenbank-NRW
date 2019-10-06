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


class SolutionAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.solution.';
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
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
            ->add('url', TextType::class, [
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
        $datagridMapper->add('name',
            null,
            ['label' => $this->labelGroup . 'name']
        );
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
        $datagridMapper->add('authentications',
            null,
            ['label' => $this->labelGroup . 'authentications'],
            null,
            ['expanded' => false, 'multiple' => true]
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
            ])
            ->add('portals', null, [
                'label' => $this->labelGroup . 'portals',
            ])
            ->add('serviceProvider', null, [
                'label' => $this->labelGroup . 'service_provider',
            ])
            ->add('serviceSystems', null, [
                'label' => $this->labelGroup . 'service_systems',
                //'associated_property' => 'name',
                'template' => 'SolutionAdmin/list-service-systems.html.twig'
            ])
            ->add('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('status', 'choice', [
                'label' => $this->labelGroup . 'status',
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])
            ->add('url', TextType::class, [
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
                //TODO: teplate not working?! 'template' => 'SolutionAdmin/specialized_procedures.html.twig',
            ])
            ->add('authentications', null, [
                'label' => $this->labelGroup . 'authentications',
            ])
            ->add('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('url', null, [
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
