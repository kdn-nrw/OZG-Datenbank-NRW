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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addDefaultDatagridFilter($datagridMapper, 'organisations');
        $this->addDefaultDatagridFilter($datagridMapper, 'projectStartAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'projectEndAt');
        $datagridMapper
            ->add('description')
            ->add('usp')
            ->add('communesBenefits')
            ->add('transferableService')
            ->add('transferableStart');
        $this->addDefaultDatagridFilter($datagridMapper, 'modelRegions');
        $this->addDefaultDatagridFilter($datagridMapper, 'solutions');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $this->addDatePickersListFields($listMapper, 'projectStartAt');
        $this->addDatePickersListFields($listMapper, 'projectEndAt');
        $listMapper
            ->add('organisations', null, [
                'template' => 'General/Association/list_many_to_many_nolinks.html.twig',
            ]);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('description');
        $this->addDatePickersShowFields($showMapper, 'projectStartAt');
        $this->addDatePickersShowFields($showMapper, 'projectEndAt');
        $showMapper
            ->add('usp')
            ->add('communesBenefits')
            ->add('transferableService')
            ->add('transferableStart');
        $showMapper
            ->add('organisations');
        $showMapper
            ->add('modelRegions', null, [
                'admin_code' => ModelRegionAdmin::class,
            ]);
        $showMapper
            ->add('solutions', null, [
                'admin_code' => SolutionAdmin::class,
                'template' => 'General/Show/show-solutions.html.twig',
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
