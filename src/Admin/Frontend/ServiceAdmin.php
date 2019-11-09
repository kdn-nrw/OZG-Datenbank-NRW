<?php

namespace App\Admin\Frontend;

use App\Datagrid\CustomDatagrid;
use App\Entity\Priority;
use App\Entity\Situation;
use App\Entity\Status;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class ServiceAdmin extends AbstractFrontendAdmin
{

    /**
     * @var string[]
     */
    protected $customLabels = [
        'entity.service_system_situation' => 'app.service_system.entity.situation',
        'entity.service_system_service_key' => 'app.service_system.entity.service_key',
        'entity.service_system_priority' => 'app.service_system.entity.priority',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('serviceSystem.situation',
            null,
            [
                'show_filter' => true,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('name');
        $datagridMapper->add('serviceKey');
        $datagridMapper->add('serviceType');
        $datagridMapper->add('serviceSystem',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSystem.serviceKey');
        $datagridMapper->add('status');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('serviceSystem.situation')
            ->add('serviceSystem', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystem'],
                ]
            ])
            ->add('serviceSystem.serviceKey', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'serviceKey'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystem'],
                ]
            ])
            ->add('name')
            ->add('serviceKey')
            ->add('serviceType')
            ->add('lawShortcuts')
            ->add('relevance1')
            ->add('relevance2')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])
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

    public function getExportFields()
    {
        return [
            'serviceSystem.situation', 'serviceSystem', 'serviceSystem.serviceKey', 'name',
            'serviceKey', 'serviceType', 'lawShortcuts', 'relevance1', 'relevance2', 'status'
        ];
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('serviceKey', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('serviceSystem', null, [
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ])
            ->add('serviceType', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('legalBasis', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('laws', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('lawShortcuts', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('relevance1', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('relevance2', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.priority', 'choice', [
                'editable' => true,
                'class' => Priority::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.situation')
            ->add('serviceSystem.situation.subject');
    }

    public function isGranted($name, $object = null)
    {
        if (in_array($name, ['LIST', 'VIEW', 'EXPORT'])) {
            return true;
        }
        return parent::isGranted($name, $object);
    }

    public function buildDatagrid()
    {
        if ($this->datagrid) {
            return;
        }
        parent::buildDatagrid();
        /** @var CustomDatagrid $datagrid */
        $datagrid = $this->datagrid;
        $modelManager = $this->getModelManager();
        $situations = $modelManager->findBy(Situation::class);
        $datagrid->addFilterMenu('serviceSystem.situation', $situations, 'app.service_system.entity.situation');
    }
}