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
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class ModelRegionAdmin extends AbstractFrontendAdmin implements EnableFullTextSearchAdminInterface
{
    use AddressTrait;

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
        $this->addAddressDatagridFilters($filter);
        $this->addDefaultDatagridFilter($filter, 'modelRegionProjects');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name');
        $this->addAddressListFields($list);
        $list->add('url', 'url');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name');
        $this->addAddressShowFields($show);
        $show->add('url', 'url');
        $show
            ->add('modelRegionProjects', null, [
                'admin_code' => ModelRegionProjectAdmin::class,
            ]);
    }

    public function isGranted($name, ?object $object = null): bool
    {
        if (in_array($name, ['LIST', 'VIEW', 'SHOW', 'EXPORT'])) {
            return true;
        }
        return parent::isGranted($name, $object);
    }


    public function getRoutePrefix(): string
    {
        return 'frontend_app_modelregion';
    }
}
