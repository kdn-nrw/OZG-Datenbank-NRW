<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Frontend;

use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\SolutionAdmin;
use App\Admin\StateGroup\CommuneSolutionAdmin;
use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\CentralAssociationTrait;
use App\Admin\Traits\LaboratoryTrait;
use App\Admin\Traits\OrganisationOneToOneTrait;
use App\Datagrid\CustomDatagrid;
use App\Entity\StateGroup\AdministrativeDistrict;
use App\Entity\StateGroup\Commune;
use App\Model\ExportSettings;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class CommuneAdmin extends AbstractFrontendAdmin implements EnableFullTextSearchAdminInterface
{
    use AddressTrait;
    use CentralAssociationTrait;
    use LaboratoryTrait;
    use OrganisationOneToOneTrait;

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.commune.entity.organisation_contacts' => 'app.organisation.entity.contacts',
        'app.commune.entity.organisation_url' => 'app.organisation.entity.url',
        'app.commune.entity.organisation_street' => 'app.organisation.entity.street',
        'app.commune.entity.organisation_zip_code' => 'app.organisation.entity.zip_code',
        'app.commune.entity.organisation_town' => 'app.organisation.entity.town',
    ];

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
        $filter->add('organisation.zipCode');
        $filter->add('organisation.town');
        $this->addDefaultDatagridFilter($filter, 'centralAssociations');
        $this->addDefaultDatagridFilter($filter, 'laboratories');
        $filter->add('constituency',
            null,
            [
                'admin_code' => self::class,
            ],
            [
                'expanded' => false,
                'multiple' => true,
                'query_builder' => $this->getConstituencyQueryBuilder()
            ]
        );
        $this->addDefaultDatagridFilter($filter, 'administrativeDistrict');
        $this->addDefaultDatagridFilter($filter, 'communeType');
        $filter->add('officialCommunityKey');
        $filter->add('regionalKey');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name');
        $this->addOrganisationOneToOneListFields($list);
        $list
            ->add('constituency', null, [
                'admin_code' => self::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'constituency'],
                ]
            ])
            ->add('officialCommunityKey')
            ->add('communeType', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'communeType'],
                ]
            ])
            ->add('administrativeDistrict', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'administrativeDistrict'],
                ]
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name')
            ->add('organisation.zipCode')
            ->add('organisation.town')
            ->add('organisation.url', 'url')
            ->add('officialCommunityKey')
            ->add('regionalKey')
            ->add('administrativeDistrict')
            ->add('constituency', null, [
                'admin_code' => self::class,
            ])
            ->add('communeType');
        $this->addCentralAssociationsShowFields($show);
        $this->addLaboratoriesShowFields($show);
        $show->add('solutions', null, [
            'label' => 'app.commune_type.entity.online_solutions',
            'admin_code' => SolutionAdmin::class,
            'is_custom_field' => true,
            'is_tab_field' => true,
            'is_custom_rendered' => true,
            'reference_field_list' => ['name', 'url', 'description', 'jurisdictions', 'maturity',],// 'status'
            'show_export' => true,
        ]);
        $show->add('communeSolutions', null, [
            'label' => 'app.commune.entity.commune_solutions',
            'admin_code' => CommuneSolutionAdmin::class,
            'is_custom_field' => true,
            'is_tab_field' => true,
            'is_custom_rendered' => true,
            'reference_field_list' => ['solution', 'connection_planned', 'specialized_procedure', 'comment',],
            //'show_export' => true,
            'showSolutions' => true,
        ]);
        $show->add('communeType.serviceSystems', null, [
            'label' => 'app.commune_type.entity.service_systems',
            'admin_code' => ServiceSystemAdmin::class,
            'is_custom_field' => true,
            'is_tab_field' => true,
            'is_custom_rendered' => true,
            'reference_field_list' => ['name', 'service_key', 'jurisdictions', 'situation', 'subject', 'priority',],// 'status'
            'show_export' => true,
        ]);
        $show->add('communeType.services', null, [
            'label' => 'app.commune_type.entity.services',
            'admin_code' => ServiceAdmin::class,
            'is_custom_field' => true,
            'is_tab_field' => true,
            'is_custom_rendered' => true,
            'reference_field_list' => [
                'name', 'service_key',  'service_created_at',
                //'service_type', 'law_shortcuts',
                'relevance1',
                // 'relevance2',
                'implementation_projects',
                //'implementation_project_status_info.status',
               // 'implementation_project_status_info.project_start_at',
                //'implementation_project_status_info.concept_status_at',
                //'implementation_project_status_info.implementation_status_at',
                //'implementation_project_status_info.piloting_status_at',
                'implementation_project_status_info.commissioning_status_at',
                //'implementation_project_status_info.nationwide_rollout_at',
                'commune_service_vsm_info',
                'commune_service_solutions',
            ],// 'status'
            'show_export' => true,
        ]);
        $show
            ->add('transparencyPortalUrl', 'url');
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['specializedProcedures', 'portals', 'specializedProcedures.manufacturers',
            'serviceProviders', 'organisation.contacts', 'communeType.serviceSystems', 'communeType.services']);
        //$settings->setAdditionFields(['manufacturers']);
        return $settings;
    }

    public function toString(object $object): string
    {
        return $object instanceof Commune
            ? $object->getName()
            : 'Commune'; // shown in the breadcrumb on the create view
    }

    /**
     * Returns the query builder for the constituencies (sub-set of communes)
     *
     * @return QueryBuilder
     */
    private function getConstituencyQueryBuilder(): QueryBuilder
    {
        /** @var EntityManager $em */
        $em = $this->getModelManager()->getEntityManager(Commune::class);

        $queryBuilder = $em->createQueryBuilder()
            ->select('c')
            ->from(Commune::class, 'c')
            ->leftJoin('c.communeType', 'ct')
            ->where('ct.constituency = 1')
            ->orderBy('c.name', 'ASC');
        return $queryBuilder;
    }

    public function isGranted($name, ?object $object = null): bool
    {
        if (in_array($name, ['LIST', 'VIEW', 'SHOW', 'EXPORT'])) {
            return true;
        }
        return parent::isGranted($name, $object);
    }

    protected function buildDatagrid(): ?DatagridInterface
    {
        $datagrid = parent::buildDatagrid();
        /** @var CustomDatagrid $datagrid */
        if ($datagrid && !$datagrid->hasFilterMenu('administrativeDistrict')) {
            /** @var CustomDatagrid $datagrid */
            $datagrid = $this->datagrid;
            $modelManager = $this->getModelManager();
            //$situations = $modelManager->findBy(Situation::class);
            //$datagrid->addFilterMenu('serviceSystem.situation', $situations, 'app.service_system.entity.situation');
            $filterChoices = $modelManager->findBy(AdministrativeDistrict::class);
            $datagrid->addFilterMenu(
                'administrativeDistrict',
                $filterChoices,
                'app.commune.entity.administrative_district_placeholder',
                AdministrativeDistrict::class
            );
        }
        return $datagrid;
    }


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_commune';
    }
}
