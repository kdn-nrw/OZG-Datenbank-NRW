<?php

namespace App\Admin;

use App\Entity\Commune;
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


class CommuneAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.commune.';
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
            $menu->addChild($this->labelGroup . 'actions.solution_list', [
                'uri' => $admin->getChild(SolutionAdmin::class)->generateUrl('list', ['id' => $id])
            ]);
            $menu->addChild($this->labelGroup . 'actions.office_list', [
                'uri' => $admin->getChild(OfficeAdmin::class)->generateUrl('list', ['id' => $id])
            ]);
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->labelGroup . 'group.general_data')
                ->add('name', TextType::class, ['label' => $this->labelGroup . 'name'])
                ->add('street', TextType::class, [
                    'required' => false,
                    'label' => $this->labelGroup . 'street'
                ])
                ->add('zipCode', TextType::class, [
                    'required' => false,
                    'label' => $this->labelGroup . 'zip_code'
                ])
                ->add('town', TextType::class, [
                    'required' => false,
                    'label' => $this->labelGroup . 'town'
                ])
                ->add('url', TextType::class, [
                    'required' => false,
                    'label' => $this->labelGroup . 'url'
                ])
                ->add('contact', TextareaType::class, [
                    'label' => $this->labelGroup . 'contact',
                    'required' => false,
                ])
                ->add('serviceProviders', ModelType::class, [
                    'label' => $this->labelGroup . 'service_providers',
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                ])
            ->end()
            ->with($this->labelGroup . 'group.offices')
                ->add('offices', CollectionType::class, [
                    'label' => false,//$this->labelGroup . 'offices',
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
        $datagridMapper->add('zipCode',
            null,
            ['label' => $this->labelGroup . 'zip_code']
        );
        $datagridMapper->add('town',
            null,
            ['label' => $this->labelGroup . 'town']
        );
        $datagridMapper->add('offices',
            null,
            ['label' => $this->labelGroup . 'offices'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceProviders',
            null,
            ['label' => $this->labelGroup . 'service_providers'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('zipCode', null, [
                'label' => $this->labelGroup . 'zip_code'
            ])
            ->add('town', null, [
                'label' => $this->labelGroup . 'town'
            ])
            ->add('url', null, [
                'label' => $this->labelGroup . 'url',
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
            ->add('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('street', null, [
                'label' => $this->labelGroup . 'street',
            ])
            ->add('zipCode', null, [
                'label' => $this->labelGroup . 'zip_code',
            ])
            ->add('town', null, [
                'label' => $this->labelGroup . 'town',
            ])
            ->add('url', null, [
                'label' => $this->labelGroup . 'url',
            ])
            ->add('contact', null, [
                'label' => $this->labelGroup . 'contact',
            ])
            ->add('offices', null, [
                'label' => $this->labelGroup . 'offices',
                'template' => 'CommuneAdmin/offices.html.twig',
            ])
            ->add('serviceProviders', null, [
                'label' => $this->labelGroup . 'service_providers',
                'template' => 'General/service-providers.html.twig',
            ]);
    }

    public function toString($object)
    {
        return $object instanceof Commune
            ? $object->getName()
            : 'Commune'; // shown in the breadcrumb on the create view
    }
}
