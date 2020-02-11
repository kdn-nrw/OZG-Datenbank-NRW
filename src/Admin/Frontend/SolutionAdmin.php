<?php

namespace App\Admin\Frontend;

use App\Datagrid\CustomDatagrid;
use App\Entity\Solution;
use App\Entity\Status;
use App\Entity\Subject;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class SolutionAdmin extends AbstractFrontendAdmin
{

    /**
     * @var string[]
     */
    protected $customLabels = [
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('serviceProvider',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service.serviceSystem',
            null,
            ['label' => 'app.service.entity.service_system'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service.serviceSystem.jurisdictions',
            null,
            ['label' => 'app.service_system.entity.jurisdictions'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service.serviceSystem.situation.subject',
            null,
            ['label' => 'app.situation.entity.subject'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('maturity',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSolutions.service',
            null,
            [
                'label' => 'app.service_solution.entity.service',
                'admin_code' => \App\Admin\ServiceAdmin::class
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status');
        $datagridMapper->add('portals',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('communes',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('specializedProcedures',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('formServers',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('paymentTypes',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('authentications',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('name');
        $datagridMapper->add('description');
    }

    /**
     * Exclude unpublished objects
     * @param string $context
     * @return \Doctrine\ORM\QueryBuilder|\Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        /** @var \Doctrine\ORM\QueryBuilder $query */
        $query->andWhere(
            $query->expr()->eq($query->getRootAliases()[0] . '.isPublished', ':isPublished')
        );
        $query->setParameter('isPublished', 1);
        return $query;
    }

    /**
     * Exclude unpublished object
     *
     * @param int $id
     * @return Solution|null
     */
    public function getObject($id)
    {
        $object = parent::getObject($id);
        /** @var Solution|null $object */
        if (null !== $object && !$object->isPublished()) {
            return null;
        }

        return $object;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('communes', null, [
                'template' => 'SolutionAdmin/list_communes.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'communes'],
                ]
            ])/*
            ->add('portals', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'portals'],
                ]
            ])*/
            ->add('serviceProvider', null, [
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
                'label' => 'app.service_system.entity.jurisdictions',
                //'associated_property' => 'name',
                'template' => 'SolutionAdmin/list-jurisdiction.html.twig',
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
                ]
            ])
            ->add('url', 'url', [
                'required' => false
            ]);
        $this->addDefaultListActions($listMapper);
    }

    public function getExportFields()
    {
        return [
            // 'communes', 'serviceSystems', 'serviceSystems.jurisdictions',
            'serviceProvider', 'customProvider', 'name', 'maturity', 'url', 'status',
        ];
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('communes', 'choice', [
                'associated_property' => 'name',
                'template' => 'SolutionAdmin/show-communes.html.twig',
            ])
            ->add('serviceProvider')
            ->add('portals')
            ->add('specializedProcedures')
            ->add('formServers')
            ->add('paymentTypes')
            ->add('authentications')
            ->add('name')
            ->add('url', 'url', [
            ])
            ->add('contact')
            ->add('serviceSystems')
            ->add('serviceSolutions', null, [
                'associated_property' => 'service'
            ])
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
    }

    public function isGranted($name, $object = null)
    {
        if (in_array($name, ['LIST', 'VIEW', 'SHOW', 'EXPORT'])) {
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
        //$situations = $modelManager->findBy(Situation::class);
        //$datagrid->addFilterMenu('serviceSystem.situation', $situations, 'app.service_system.entity.situation');
        $subjects = $modelManager->findBy(Subject::class);
        $datagrid->addFilterMenu('serviceSolutions.service.serviceSystem.situation.subject', $subjects, 'app.situation.entity.subject');
    }
}
