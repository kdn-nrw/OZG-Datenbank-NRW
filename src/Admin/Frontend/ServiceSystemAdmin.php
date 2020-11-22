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
use App\Datagrid\CustomDatagrid;
use App\Entity\Subject;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class ServiceSystemAdmin extends AbstractFrontendAdmin implements EnableFullTextSearchAdminInterface
{

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.service_system.entity.situation_subject' => 'app.situation.entity.subject',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('serviceKey');
        //$this->addDefaultDatagridFilter($datagridMapper, 'laboratories');
        $this->addDefaultDatagridFilter($datagridMapper, 'jurisdictions');
        $this->addDefaultDatagridFilter($datagridMapper, 'situation');
        $this->addDefaultDatagridFilter($datagridMapper, 'situation.subject');
        $this->addDefaultDatagridFilter($datagridMapper, 'priority');
        //$datagridMapper->add('status');
        $this->addDefaultDatagridFilter($datagridMapper, 'stateMinistries');
        $this->addDefaultDatagridFilter($datagridMapper, 'solutions');
        $this->addDefaultDatagridFilter($datagridMapper, 'bureaus');
        $this->addDefaultDatagridFilter($datagridMapper, 'services.portals');
        $this->addDefaultDatagridFilter($datagridMapper, 'communeTypes');
        $this->addDefaultDatagridFilter($datagridMapper, 'implementationProjects');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
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
            ->add('priority')
            /*
            ->add('status', 'choice', [
                'editable' => false,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])*/
            ->add('references', 'string', [
                'label' => 'app.service_system.entity.references',
                'template' => 'ServiceSystemAdmin/list-references.html.twig',
                'filterParamName' => 'serviceSystem__serviceKey',
                'referenceLabel' => 'app.service.type_label',
                'route' => [
                    'prefix' => 'frontend_app_service',
                    'name' => 'list',
                ],
            ]);
        $this->addDefaultListActions($listMapper);
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
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
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
                'template' => 'General/Show/show-services.html.twig',
            ])
            ->add('solutions', null, [
                'admin_code' => SolutionAdmin::class,
                'template' => 'General/Show/show-solutions.html.twig',
            ]);
        //$this->addLaboratoriesShowFields($showMapper);
        $showMapper->add('situation.subject', null, [
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
        $datagrid->addFilterMenu('situation.subject', $subjects, 'app.situation.entity.subject', Subject::class);
    }


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_servicesystem';
    }
}
