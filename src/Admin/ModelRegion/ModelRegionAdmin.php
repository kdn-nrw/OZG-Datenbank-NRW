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

namespace App\Admin\ModelRegion;

use App\Admin\AbstractAppAdmin;
use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\ModelRegionProjectTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class ModelRegionAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use AddressTrait;
    use ModelRegionProjectTrait;

    protected $baseRoutePattern = 'model-region/model-region';

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->tab('default', ['label' => 'app.model_region.tabs.default']);
        $form->with('general', [
            'label' => 'app.model_region.tabs.default',
        ]);
        $form
            ->add('name', TextType::class);
        $this->addAddressFormFields($form);
        $form
            ->add('url', UrlType::class, [
                'required' => false,
            ]);
        $this->addModelRegionProjectsFormFields($form);
        $form->end();
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $this->addAddressDatagridFilters($filter);
        $this->addDefaultDatagridFilter($filter, 'modelRegionProjects');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name');
        $this->addAddressListFields($list);
        $list->add('url', 'url');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show->add('name');
        $this->addAddressShowFields($show);
        $show->add('url', 'url');
        $this->addModelRegionProjectsShowFields($show);
    }
}
