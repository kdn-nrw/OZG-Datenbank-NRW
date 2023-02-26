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
use App\Admin\StateGroup\ServiceProviderAdmin;
use App\Admin\Traits\ModelRegionProjectTrait;
use App\Admin\Traits\ServiceProviderTrait;
use App\Datagrid\CustomDatagrid;
use App\Entity\ConfidenceLevel;
use App\Entity\Solution;
use App\Entity\Status;
use App\Entity\Subject;
use App\Exporter\Source\ServiceSolutionValueFormatter;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
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

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $this->addDefaultDatagridFilter($filter, 'serviceSolutions.service.serviceSystem');
        $this->addDefaultDatagridFilter($filter, 'serviceSolutions.service.serviceSystem.jurisdictions');
        $this->addDefaultDatagridFilter($filter, 'serviceSolutions.service.serviceSystem.situation.subject');
        $this->addDefaultDatagridFilter($filter, 'maturity');
        $this->addDefaultDatagridFilter($filter, 'serviceSolutions.service');
        $filter->add('status');
        $this->addDefaultDatagridFilter($filter, 'portals');
        $filter->add('communeType', null,
            [
                'field_type' => ChoiceType::class,
            ],
            [
                'choices' => [
                    'app.solution.entity.commune_type_all' => 'all',
                    'app.solution.entity.commune_type_selected' => 'selected',
                ],
                'expanded' => false,
                'multiple' => false,
            ]);
        $this->addDefaultDatagridFilter($filter, 'communeSolutions.commune', [
            'label' => 'app.solution.entity.communes',
        ]);
        $this->addDefaultDatagridFilter($filter, 'formServerSolutions.formServer');
        $this->addDefaultDatagridFilter($filter, 'paymentTypes');
        $this->addDefaultDatagridFilter($filter, 'authentications');
        $this->addDefaultDatagridFilter($filter, 'analogServices');
        $filter->add('name');
        $filter->add('description');
        $this->addDefaultDatagridFilter($filter, 'modelRegionProjects');
        $filter->add('confidenceLevel');
    }

    /**
     * Exclude unpublished object
     *
     * @param int $id
     * @return Solution|null
     */
    public function getObject($id): ?object
    {
        $object = parent::getObject($id);
        /** @var Solution|null $object */
        if (null !== $object && !$object->isPublished()) {
            return null;
        }

        return $object;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('selectedCommuneSolutions', null, [
                'label' => 'app.solution.entity.communes',
                'admin_code' => CommuneAdmin::class,
                'template' => 'SolutionAdmin/list_communes.html.twig',
                'associated_property' => 'commune',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'communeSolutions'],
                    ['fieldName' => 'commune'],
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
                /*'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSolutions'],
                    ['fieldName' => 'service'],
                    ['fieldName' => 'serviceSystem'],
                    ['fieldName' => 'jurisdictions'],
                ],*/
                'enable_filter_add' => true,
            ])
            ->add('name')/*
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
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
                ],
                'enable_filter_add' => true,
            ])
            ->add('url', 'url');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $customServiceFormatter = new ServiceSolutionValueFormatter();
        $customServiceFormatter->setDisplayType(ServiceSolutionValueFormatter::DISPLAY_SERVICE_KEY);
        $settings->setAdditionFields([
            // 'communes', 'serviceSystems', 'serviceSystems.jurisdictions', 'serviceProviders',
            'publishedServiceSolutions',
            'customProvider', 'name', 'maturity', 'url', 'status',
        ]);
        $settings->addExcludeFields(['serviceSolutions',]);
        $translator = $this->getTranslator();
        $settings->addCustomLabel(
            'publishedServiceSolutions',
            $translator->trans('app.solution.entity.service_solutions')
        );
        $settings->addCustomPropertyValueFormatter('publishedServiceSolutions', $customServiceFormatter);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name')
            ->add('description')
            ->add('url', 'url')
            ->add('paymentTypes')
            ->add('authentications')
            ->add('analogServices')
            ->add('openDataItems');
        $show
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                'editable' => false,
                'class' => Status::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('confidenceLevel', FieldDescriptionInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => ConfidenceLevel::class,
                'catalogue' => 'messages',
                //'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('portals');
        $show
            ->add('customProvider');
        $enableTabs = true;
        // Tab fields
        $show
            ->add('communes', null, [
                'label' => 'app.solution.entity.communes',
                'admin_code' => CommuneAdmin::class,
                'associated_property' => 'name',
                'check_has_all_modifier' => true,
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ])
            ->add('serviceProviders', null, [
                'admin_code' => ServiceProviderAdmin::class,
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ])
            ->add('formServerSolutions', null, [
                'associated_property' => 'formServer',
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ])
            ->add('serviceSystems', null, [
                'admin_code' => ServiceSystemAdmin::class,
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ])
            ->add('serviceSolutions', null, [
                'associated_property' => 'service',
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ])
            ->add('modelRegionProjects', null,[
                'admin_code' => ModelRegionProjectAdmin::class,
                'is_custom_field' => $enableTabs,
                'is_tab_field' => $enableTabs,
                'is_custom_rendered' => $enableTabs,
            ]);
    }

    public function isGranted($name, ?object $object = null): bool
    {
        if (in_array($name, ['LIST', 'VIEW', 'SHOW', 'EXPORT'])) {
            return true;
        }
        return parent::isGranted($name, $object);
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        // Fix Sonata-Bug https://github.com/sonata-project/SonataAdminBundle/issues/3368
        // When global search is executed, the filter query will be concatenated with the additional
        // conditions in this function with OR (instead of AND)
        // This means all extra conditions will be ignored, and we have to execute the full search query here
        // @see \Sonata\AdminBundle\Search\SearchHandler::search
        $reqSearchTerm = null;
        if ($this->hasRequest()) {
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
