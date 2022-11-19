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
use App\Entity\ImplementationProject;
use App\Entity\ImplementationStatus;
use App\Entity\Subject;
use App\Exporter\Source\ServiceListValueFormatter;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Show\ShowMapper;


class ImplementationProjectAdmin extends AbstractFrontendAdmin implements EnableFullTextSearchAdminInterface
{
    use DatePickerTrait;

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name')
            ->add('serviceSystems.situation.subject', FieldDescriptionInterface::TYPE_ARRAY, [
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
                'virtual_field' => true,
            ])
            ->add('efaType', FieldDescriptionInterface::TYPE_CHOICE, [
                'label' => 'app.implementation_project.entity.efa_type',
                'editable' => false,
                'choices' => ImplementationProject::EFA_TYPES,
                'catalogue' => 'messages',
            ])
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
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
        $this->addDatePickersListFields($list, 'projectStartAt', true);
        $this->addDatePickersListFields($list, 'conceptStatusAt', true);
        $this->addDatePickersListFields($list, 'implementationStatusAt', true);
        $this->addDatePickersListFields($list, 'pilotingStatusAt', true);
        $this->addDatePickersListFields($list, 'commissioningStatusAt', true);
        $this->addDatePickersListFields($list, 'nationwideRolloutAt', true);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['statusInfo']);
        $customServiceFormatter = new ServiceListValueFormatter();
        $customServiceFormatter->setDisplayType(ServiceListValueFormatter::DISPLAY_SERVICE_KEY);
        $settings->addCustomPropertyValueFormatter('serviceKeys', $customServiceFormatter);
        $customServiceSystemFormatter = new ServiceListValueFormatter();
        $customServiceSystemFormatter->setDisplayType(ServiceListValueFormatter::DISPLAY_SERVICE_KEY);
        $settings->addCustomPropertyValueFormatter('serviceSystemKeys', $customServiceSystemFormatter);
        $settings->setAdditionFields([
            'status', 'projectStartAt', 'conceptStatusAt',
            'implementationStatusAt', 'pilotingStatusAt', 'commissioningStatusAt', 'nationwideRolloutAt',
            'serviceKeys', 'serviceSystemKeys',
        ]);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name')
            ->add('description');
        $show
            ->add('notes', FieldDescriptionInterface::TYPE_HTML, [
                'template' => 'ImplementationProjectAdmin/show-notes.html.twig',
                'is_custom_field' => true,
            ]);
        $show->add('statusInfo', null, [
            'admin_code' => ImplementationStatusAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-status-info.html.twig',
            'is_custom_field' => true,
        ]);
        $show
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
        $show->add('services', null, [
            'admin_code' => ServiceAdmin::class,
            'showFimTypes' => true,
            'template' => 'ImplementationProjectAdmin/Show/show-project-services.html.twig',
            'is_custom_field' => true,
            'showProject' => false,
            'route' => [
                'name' => 'show',
            ],
        ]);
        $show
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
        $show
            ->add('fundings', null, [
                'admin_code' => FundingAdmin::class,
            ]);
        $show->add('bureaus', null, [
            'admin_code' => BureauAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-bureaus.html.twig',
        ]);
        $show->add('portals', null, [
            'admin_code' => PortalAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-portals.html.twig',
        ]);
        $this->addDatePickersShowFields($show, 'nationwideRolloutAt', false);
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
        if ($datagrid && !$datagrid->hasFilterMenu('serviceSystems.situation.subject')) {
            $modelManager = $this->getModelManager();
            //$situations = $modelManager->findBy(Situation::class);
            //$datagrid->addFilterMenu('serviceSystem.situation', $situations, 'app.service_system.entity.situation');
            $subjects = $modelManager->findBy(Subject::class);
            $datagrid->addFilterMenu('serviceSystems.situation.subject', $subjects, 'app.situation.entity.subject', Subject::class);
        }
        return $datagrid;
    }


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_implementationproject';
    }
}
