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
use App\Admin\FundingAdmin;
use App\Admin\ImplementationStatusAdmin;
use App\Admin\PortalAdmin;
use App\Admin\StateGroup\BureauAdmin;
use App\Admin\Traits\DatePickerTrait;
use App\Datagrid\CustomDatagrid;
use App\Entity\ImplementationStatus;
use App\Entity\Subject;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class ImplementationProjectAdmin extends AbstractFrontendAdmin implements EnableFullTextSearchAdminInterface
{
    use DatePickerTrait;

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addDefaultDatagridFilter($datagridMapper, 'laboratories');
        $this->addDefaultDatagridFilter($datagridMapper, 'solutions');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSystems');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSystems.situation.subject');
        $this->addDefaultDatagridFilter($datagridMapper, 'services.service');
        $datagridMapper->add('status');
        $this->addDefaultDatagridFilter($datagridMapper, 'projectStartAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'conceptStatusAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'implementationStatusAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'commissioningStatusAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'fundings');
        $this->addDefaultDatagridFilter($datagridMapper, 'services.service.bureaus');
        $this->addDefaultDatagridFilter($datagridMapper, 'services.service.portals', ['label' => 'app.implementation_project.entity.portals']);
        $this->addDefaultDatagridFilter($datagridMapper, 'projectLeaders');
        $this->addDefaultDatagridFilter($datagridMapper, 'participationOrganisations');
        $this->addDefaultDatagridFilter($datagridMapper, 'interestedOrganisations');
        $this->addDefaultDatagridFilter($datagridMapper, 'services.service.communeTypes', ['label' => 'app.service_system.entity.commune_types']);
        $this->addDefaultDatagridFilter($datagridMapper, 'solutions.openDataItems');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('serviceSystems.situation.subject', 'string', [
                'label' => 'app.situation.entity.subject',
                //'associated_property' => 'name',
                'template' => 'ImplementationProjectAdmin/list-service-system-subjects.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystems'],
                    ['fieldName' => 'situation'],
                    ['fieldName' => 'subject'],
                ],
                'enable_filter_add' => true,
            ])
            ->add('status', 'choice', [
                'editable' => false,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
                'template' => 'ImplementationProjectAdmin/list-status.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'status'],
                ]
            ]);
        $this->addDatePickersListFields($listMapper, 'projectStartAt', true);
        $this->addDatePickersListFields($listMapper, 'conceptStatusAt', true);
        $this->addDatePickersListFields($listMapper, 'implementationStatusAt', true);
        $this->addDatePickersListFields($listMapper, 'commissioningStatusAt', true);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['statusInfo']);
        $settings->setAdditionFields(['status', 'projectStartAt', 'conceptStatusAt', 'implementationStatusAt', 'commissioningStatusAt']);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description');
        $showMapper
            ->add('notes', 'html', [
                'template' => 'ImplementationProjectAdmin/show-notes.html.twig',
                'is_custom_field' => true,
            ]);
        $showMapper->add('statusInfo', null, [
            'admin_code' => ImplementationStatusAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-status-info.html.twig',
            'is_custom_field' => true,
        ]);
        $showMapper
            ->add('publishedSolutions', null, [
                'admin_code' => SolutionAdmin::class,
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('serviceSystems', null, [
                'admin_code' => ServiceSystemAdmin::class,
                'route' => [
                    'name' => 'show',
                ],
            ]);
        $showMapper->add('services', null, [
            'admin_code' => ServiceAdmin::class,
            'showFimTypes' => true,
            'template' => 'ImplementationProjectAdmin/Show/show-project-services.html.twig',
            'is_custom_field' => true,
            'showProject' => false,
            'route' => [
                'name' => 'show',
            ],
        ]);
        $showMapper
            ->add('projectLeaders', null, [
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('participationOrganisations', null, [
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('interestedOrganisations', null, [
                'route' => [
                    'name' => 'show',
                ],
            ]);
        $showMapper
            ->add('fundings', null, [
                'admin_code' => FundingAdmin::class,
            ]);
        $showMapper->add('bureaus', null, [
            'admin_code' => BureauAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-bureaus.html.twig',
        ]);
        $showMapper->add('portals', null, [
            'admin_code' => PortalAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-portals.html.twig',
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
        $datagrid->addFilterMenu('serviceSystems.situation.subject', $subjects, 'app.situation.entity.subject', Subject::class);
    }


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_implementationproject';
    }
}
