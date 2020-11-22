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

use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\Traits\ModelRegionProjectTrait;
use App\Admin\Traits\ServiceProviderTrait;
use App\Datagrid\CustomDatagrid;
use App\Entity\Solution;
use App\Entity\Status;
use App\Entity\Subject;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class SolutionAdmin extends AbstractFrontendAdmin implements EnableFullTextSearchAdminInterface
{
    use ModelRegionProjectTrait;
    use ServiceProviderTrait;

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.solution.entity.form_server_solutions_form_server' => 'app.solution.entity.form_server_solutions',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSolutions.service.serviceSystem');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSolutions.service.serviceSystem.jurisdictions');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSolutions.service.serviceSystem.situation.subject');
        $this->addDefaultDatagridFilter($datagridMapper, 'maturity');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSolutions.service');
        $datagridMapper->add('status');
        $this->addDefaultDatagridFilter($datagridMapper, 'portals');
        $datagridMapper->add('communeType', null,
            [
            ],
            ChoiceType::class,
            [
                'choices' => [
                    'app.solution.entity.commune_type_all' => 'all',
                    'app.solution.entity.commune_type_selected' => 'selected',
                ],
                'expanded' => false,
                'multiple' => false,
            ]);
        $this->addDefaultDatagridFilter($datagridMapper, 'communes');
        $this->addDefaultDatagridFilter($datagridMapper, 'formServerSolutions.formServer');
        $this->addDefaultDatagridFilter($datagridMapper, 'paymentTypes');
        $this->addDefaultDatagridFilter($datagridMapper, 'authentications');
        $this->addDefaultDatagridFilter($datagridMapper, 'analogServices');
        $datagridMapper->add('name');
        $datagridMapper->add('description');
        $this->addDefaultDatagridFilter($datagridMapper, 'modelRegionProjects');
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
                'admin_code' => CommuneAdmin::class,
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
            ->add('serviceSystems', null, [
                'admin_code' => ServiceSystemAdmin::class,
                //'associated_property' => 'name',
                'template' => 'SolutionAdmin/list-service-systems.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSolutions'],
                    ['fieldName' => 'service'],
                    ['fieldName' => 'serviceSystem'],
                ]
            ])
            ->add('jurisdictions', 'string', [
                'label' => 'app.service_system.entity.jurisdictions',
                //'associated_property' => 'name',
                'template' => 'SolutionAdmin/list-jurisdiction.html.twig',
            ])
            ->add('name')/*
            ->add('status', TemplateRegistry::TYPE_CHOICE, [
                'editable' => false,
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

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->setAdditionFields([
            // 'communes', 'serviceSystems', 'serviceSystems.jurisdictions', 'serviceProviders',
            'customProvider', 'name', 'maturity', 'url', 'status',
        ]);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('communes', TemplateRegistry::TYPE_CHOICE, [
                'admin_code' => CommuneAdmin::class,
                'associated_property' => 'name',
                'check_has_all_modifier' => true,
            ]);
        $this->addServiceProvidersShowFields($showMapper);
        $showMapper
            ->add('customProvider')
            ->add('portals')
            ->add('formServerSolutions', null, [
                'associated_property' => 'formServer'
            ])
            ->add('paymentTypes')
            ->add('authentications')
            ->add('analogServices')
            ->add('openDataItems')
            ->add('name')
            ->add('description')
            ->add('url', 'url')
            ->add('serviceSystems', null, [
                'admin_code' => ServiceSystemAdmin::class,
            ])
            ->add('serviceSolutions', null, [
                'associated_property' => 'service'
            ])
            ->add('status', TemplateRegistry::TYPE_CHOICE, [
                'editable' => false,
                'class' => Status::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
        $this->addModelRegionProjectsShowFields($showMapper);
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
        $datagrid->addFilterMenu('serviceSolutions.service.serviceSystem.situation.subject', $subjects, 'app.situation.entity.subject', Subject::class);
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        // Fix Sonata-Bug https://github.com/sonata-project/SonataAdminBundle/issues/3368
        // When global search is executed, the filter query will be concatenated with the additional
        // conditions in this function with OR (instead of AND)
        // This means all extra conditions will be ignored and we have to execute the full search query here
        // @see \Sonata\AdminBundle\Search\SearchHandler::search
        $reqSearchTerm = null;
        if ($this->hasRequest()) {
            /** @noinspection NullPointerExceptionInspection */
            $reqSearchTerm = $this->getRequest()->get('q');
        } elseif (isset($_REQUEST['q'])) {
            $reqSearchTerm = $_REQUEST['q'];
        }
        if ($reqSearchTerm) {
            $searchTerm = strtolower(trim(strip_tags($reqSearchTerm)));
            /** @var \Doctrine\ORM\QueryBuilder $subQueryBuilder */
            $subQueryBuilder = $this->getModelManager()->createQuery(Solution::class, 's');
            $subQueryBuilder->select('s.id')
                ->where(
                    $subQueryBuilder->expr()->andX(
                        's.isPublished = :isPublished',
                        's.name LIKE :term'
                    )
                );
            $subQueryBuilder->setParameter('isPublished', 1);
            $subQueryBuilder->setParameter('term', '%' . $searchTerm . '%');
            $result = $subQueryBuilder->getQuery()->getArrayResult();
            if (!empty($result)) {
                $idList = array_column($result, 'id');
            } else {
                $idList = [0];
            }
            /** @var \Doctrine\ORM\QueryBuilder $query */
            $query->andWhere(
                $query->getRootAliases()[0] . ' IN (:idList)'
            );
            $query->setParameter('idList', $idList);
        } else {
            /** @var \Doctrine\ORM\QueryBuilder $query */
            $query->andWhere(
                $query->expr()->eq($query->getRootAliases()[0] . '.isPublished', ':isPublished')
            );
            $query->setParameter('isPublished', 1);
        }
        return $query;
    }


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_solution';
    }
}
