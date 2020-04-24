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
        'app.solution.entity.form_server_solutions_form_server' => 'app.solution.entity.form_server_solutions',
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
            [
                'label' => 'app.service.entity.service_system',
                'admin_code' => ServiceSystemAdmin::class,
            ],
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
        $datagridMapper->add('formServerSolutions.formServer',
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
        $datagridMapper->add('analogServices',
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
                'admin_code' => ServiceSystemAdmin::class,
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
        $fields = parent::getExportFields();
        $additionalFields = [
            // 'communes', 'serviceSystems', 'serviceSystems.jurisdictions',
            'serviceProvider', 'customProvider', 'name', 'maturity', 'url', 'status',
        ];
        foreach ($additionalFields as $field) {
            if (!in_array($field, $fields, false)) {
                $fields[] = $field;
            }
        }
        return $fields;
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
            ->add('customProvider')
            ->add('portals')
            ->add('specializedProcedures')
            ->add('formServerSolutions')
            ->add('paymentTypes')
            ->add('authentications')
            ->add('analogServices')
            ->add('openDataItems')
            ->add('name')
            ->add('description')
            ->add('url', 'url')
            ->add('contact')
            ->add('serviceSystems', null, [
                'admin_code' => ServiceSystemAdmin::class,
            ])
            ->add('serviceSolutions', null, [
                'associated_property' => 'service'
            ])
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
        $this->customShowFields[] = 'serviceSystems';
        $this->customShowFields[] = 'serviceSolutions';
        $this->customShowFields[] = 'formServerSolutions';
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


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_solution';
    }
}
