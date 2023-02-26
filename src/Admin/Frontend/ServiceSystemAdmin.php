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
use App\Admin\Traits\LaboratoryTrait;
use App\Datagrid\CustomDatagrid;
use App\Entity\Subject;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Show\ShowMapper;


class ServiceSystemAdmin extends AbstractFrontendAdmin implements EnableFullTextSearchAdminInterface
{
    use LaboratoryTrait;

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.service_system.entity.situation_subject' => 'app.situation.entity.subject',
    ];

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
        $filter->add('serviceKey');
        $this->addDefaultDatagridFilter($filter, 'jurisdictions');
        $this->addDefaultDatagridFilter($filter, 'situation');
        $this->addDefaultDatagridFilter($filter, 'situation.subject');
        $this->addDefaultDatagridFilter($filter, 'priority');
        //$filter->add('status');
        $this->addDefaultDatagridFilter($filter, 'stateMinistries');
        $this->addDefaultDatagridFilter($filter, 'solutions');
        $this->addDefaultDatagridFilter($filter, 'bureaus');
        $this->addDefaultDatagridFilter($filter, 'services.portals');
        $this->addDefaultDatagridFilter($filter, 'communeTypes');
        $this->addDefaultDatagridFilter($filter, 'implementationProjects');
        $this->addDefaultDatagridFilter($filter, 'laboratories');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name')
            ->add('serviceKey')
            ->add('jurisdictions', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'jurisdictions'],
                ],
                'enable_filter_add' => true,
            ])
            ->add('situation', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'situation'],
                ],
                'enable_filter_add' => true,
            ])
            ->add('situation.subject', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'situation'],
                    ['fieldName' => 'subject'],
                ],
                'enable_filter_add' => true,
            ])
            ->add('priority', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'level'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'priority'],
                ],
                'enable_filter_add' => true,
            ])
            /*
            ->add('status', 'choice', [
                'editable' => false,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])*/
            ->add('references', FieldDescriptionInterface::TYPE_STRING, [
                'label' => 'app.service_system.entity.references',
                'template' => 'ServiceSystemAdmin/list-references.html.twig',
                'filterParamName' => 'serviceSystem__serviceKey',
                'referenceLabel' => 'app.service.type_label',
                'virtual_field' => true,
                'route' => [
                    'prefix' => 'frontend_app_service',
                    'name' => 'list',
                ],
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['contact']);
        $settings->setAdditionFields([
            'name', 'serviceKey', 'situation', 'situation.subject',
            'priority',
        ]);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('serviceKey', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('jurisdictions')
            ->add('stateMinistries')
            ->add('bureaus')
            ->add('ruleAuthorities')
            ->add('authorityBureaus')
            ->add('authorityStateMinistries')
            ->add('communeTypes')
            ->add('services', null, [
                'admin_code' => ServiceAdmin::class,
            ])
            ->add('solutions', null, [
                'admin_code' => SolutionAdmin::class,
            ]);
        $show->add('situation.subject', null, [
            'template' => 'ServiceAdmin/show_many_to_one.html.twig',
        ])
            ->add('situation', null, [
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ])
            ->add('priority', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])/*
            ->add('status', 'choice', [
                'editable' => false,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])*/
            ->add('description', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('implementationProjects', null, [
                'admin_code' => ImplementationProjectAdmin::class,
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('publishedModelRegionProjects', null, [
                'admin_code' => ModelRegionProjectAdmin::class,
                'route' => [
                    'name' => 'show',
                ],
            ]);
        $this->addLaboratoriesShowFields($show);
    }

    public function isGranted($name, ?object $object = null): bool
    {
        if (in_array($name, ['LIST', 'VIEW', 'SHOW', 'EXPORT'])) {
            return true;
        }
        return parent::isGranted($name, $object);
    }


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_servicesystem';
    }
}
