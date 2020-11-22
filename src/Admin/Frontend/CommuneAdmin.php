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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('organisation.zipCode');
        $datagridMapper->add('organisation.town');
        $this->addDefaultDatagridFilter($datagridMapper, 'centralAssociations');
        $this->addDefaultDatagridFilter($datagridMapper, 'laboratories');
        $datagridMapper->add('constituency',
            null,
            [
                'admin_code' => self::class,
            ],
            null,
            [
                'expanded' => false,
                'multiple' => true,
                'query_builder' => $this->getConstituencyQueryBuilder()
            ]
        );
        $this->addDefaultDatagridFilter($datagridMapper, 'administrativeDistrict');
        $this->addDefaultDatagridFilter($datagridMapper, 'communeType');
        $datagridMapper->add('officialCommunityKey');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $this->addOrganisationOneToOneListFields($listMapper);
        $listMapper
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
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('organisation.zipCode')
            ->add('organisation.town')
            ->add('organisation.url', 'url')
            ->add('officialCommunityKey')
            ->add('administrativeDistrict')
            ->add('constituency', null, [
                'admin_code' => self::class,
            ])
            ->add('communeType');
        $this->addCentralAssociationsShowFields($showMapper);
        $this->addLaboratoriesShowFields($showMapper);
        $showMapper->add('communeType.serviceSystems', null, [
            'label' => 'app.commune_type.entity.service_systems',
            'admin_code' => ServiceSystemAdmin::class,
            'template' => 'CommuneAdmin/show-commune-type-system-services.html.twig',
            'is_custom_field' => true,
        ]);
        $showMapper->add('communeType.services', null, [
            'label' => 'app.commune_type.entity.services',
            'admin_code' => ServiceAdmin::class,
            'template' => 'CommuneAdmin/show-commune-type-services.html.twig',
            'is_custom_field' => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['specializedProcedures', 'portals', 'specializedProcedures.manufacturers',
            'contact', 'serviceProviders', 'organisation.contacts', 'communeType.serviceSystems', 'communeType.services']);
        //$settings->setAdditionFields(['manufacturers']);
        return $settings;
    }

    public function toString($object)
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
        $em = $this->modelManager->getEntityManager(Commune::class);

        $queryBuilder = $em->createQueryBuilder()
            ->select('c')
            ->from(Commune::class, 'c')
            ->leftJoin('c.communeType', 'ct')
            ->where('ct.constituency = 1')
            ->orderBy('c.name', 'ASC');
        return $queryBuilder;
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
        $filterChoices = $modelManager->findBy(AdministrativeDistrict::class);
        $datagrid->addFilterMenu(
            'administrativeDistrict',
            $filterChoices,
            'app.commune.entity.administrative_district_placeholder',
            AdministrativeDistrict::class
        );
    }


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_commune';
    }
}
