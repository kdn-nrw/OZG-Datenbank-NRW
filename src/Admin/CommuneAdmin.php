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
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class CommuneAdmin extends AbstractAppAdmin
{
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        $menu->addChild('app.commune.actions.show', [
            'uri' => $admin->generateUrl('show', ['id' => $id])
        ]);

        if ($this->isGranted('EDIT')) {
            $menu->addChild('app.commune.actions.edit', [
                'uri' => $admin->generateUrl('edit', ['id' => $id])
            ]);
        }

        if ($this->isGranted('LIST')) {
            $menu->addChild('app.commune.actions.solution_list', [
                'uri' => $admin->getChild(SolutionAdmin::class)->generateUrl('list', ['id' => $id])
            ]);
            $menu->addChild('app.commune.actions.office_list', [
                'uri' => $admin->getChild(OfficeAdmin::class)->generateUrl('list', ['id' => $id])
            ]);
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('app.commune.group.general_data')
                ->add('name', TextType::class)
                ->add('street', TextType::class, [
                    'required' => false,
                ])
                ->add('zipCode', TextType::class, [
                    'required' => false,
                ])
                ->add('town', TextType::class, [
                    'required' => false,
                ])
                ->add('url', UrlType::class, [
                    'required' => false,
                ])
                ->add('contact', TextareaType::class, [
                    'required' => false,
                ])
                ->add('serviceProviders', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                ])
            ->end()
            ->with('app.commune.group.offices')
                ->add('offices', CollectionType::class, [
                    'label' => false,
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
        $datagridMapper->add('name');
        $datagridMapper->add('zipCode');
        $datagridMapper->add('town');
        $datagridMapper->add('offices',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceProviders',
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
            ->add('zipCode')
            ->add('town')
            ->add('url', 'url')
            ->add('_action', null, [
                'label' => 'app.common.actions',
                'translation_domain' => 'messages',
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
            ->add('name')
            ->add('street')
            ->add('zipCode')
            ->add('town')
            ->add('url', 'url')
            ->add('contact')
            ->add('offices')
            ->add('serviceProviders', null, [
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
