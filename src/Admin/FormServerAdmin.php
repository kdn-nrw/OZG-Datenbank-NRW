<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class FormServerAdmin extends AbstractAppAdmin
{
    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.form_server.entity.form_server_solutions_solution' => 'app.form_server.entity.form_server_solutions',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('app.form_server.tabs.general', ['tab' => true])
                ->with('general', [
                    'label' => false,
                ])
                    ->add('name', TextType::class)
                    ->add('url', UrlType::class, [
                        'required' => false,
                    ])
                ->end()
            ->end()
            ->tab('app.form_server.tabs.solutions')
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
                        'ba_custom_hide_fields' => ['formServer'],
                    ])
                ->end()
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('formServerSolutions.solution',
            null, [
                'admin_code' => SolutionAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $listMapper->add('url', 'url');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('url', 'url')
            ->add('formServerSolutions', null, [
                'associated_property' => 'solution'
            ]);
        $this->customShowFields[] = 'formServerSolutions';
    }
}
