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
use App\Admin\Traits\DatePickerTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class ModelRegionProjectAdmin extends AbstractFrontendAdmin implements EnableFullTextSearchAdminInterface
{
    use AddressTrait;
    use DatePickerTrait;

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'projectStartAt');
        $this->addDefaultDatagridFilter($filter, 'projectConceptStartAt');
        $this->addDefaultDatagridFilter($filter, 'projectImplementationStartAt');
        $this->addDefaultDatagridFilter($filter, 'projectEndAt');
        $this->addDefaultDatagridFilter($filter, 'categories');
        $this->addDefaultDatagridFilter($filter, 'organisations');
        $filter
            ->add('description')
            ->add('usp')
            ->add('communesBenefits')
            ->add('transferableService')
            ->add('transferableStart');
        $this->addDefaultDatagridFilter($filter, 'modelRegions');
        $this->addDefaultDatagridFilter($filter, 'solutions');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name');
        $this->addDatePickersListFields($list, 'projectStartAt', true);
        $this->addDatePickersListFields($list, 'projectConceptStartAt', true);
        $this->addDatePickersListFields($list, 'projectImplementationStartAt', true);
        $this->addDatePickersListFields($list, 'projectEndAt', true);
        $list
            ->add('categories', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'categories'],
                ],
                'enable_filter_add' => true,
            ]);
        $list
            ->add('organisations', null, [
                'template' => 'General/Association/list_many_to_many_nolinks.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'organisations'],
                ],
                'enable_filter_add' => true,
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show->add('name')
            ->add('description');
        $this->addDatePickersShowFields($show, 'projectStartAt');
        $this->addDatePickersShowFields($show, 'projectConceptStartAt');
        $this->addDatePickersShowFields($show, 'projectImplementationStartAt');
        $this->addDatePickersShowFields($show, 'projectEndAt');
        $show
            ->add('projectLead')
            ->add('usp')
            ->add('communesBenefits')
            ->add('transferableService')
            ->add('transferableStart');
        $show
            ->add('categories')
            ->add('organisations');
        $show
            ->add('modelRegions', null, [
                'admin_code' => ModelRegionAdmin::class,
            ])
            ->add('websites', null, [
                'template' => 'ModelRegion/show-project-websites.html.twig',
            ]);
        $show->add('documents', null, [
            'template' => 'General/Show/show-attachments.html.twig',
        ]);
        $show
            ->add('solutions', null, [
                'admin_code' => SolutionAdmin::class,
                'route' => [
                    'name' => 'show',
                ],
                'showServices' => true,
            ]);
    }

    public function isGranted($name, $object = null)
    {
        if (in_array($name, ['LIST', 'VIEW', 'SHOW', 'EXPORT'])) {
            return true;
        }
        return parent::isGranted($name, $object);
    }

    protected function getRoutePrefix(): string
    {
        return 'frontend_app_modelregionproject';
    }
}
