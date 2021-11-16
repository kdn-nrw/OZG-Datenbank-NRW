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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('default', ['label' => 'app.model_region.tabs.default']);
        $formMapper->with('general', [
            'label' => 'app.model_region.tabs.default',
        ]);
        $formMapper
            ->add('name', TextType::class);
        $this->addAddressFormFields($formMapper);
        $formMapper
            ->add('url', UrlType::class, [
                'required' => false,
            ]);
        $this->addModelRegionProjectsFormFields($formMapper);
        $formMapper->end();
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addAddressDatagridFilters($datagridMapper);
        $this->addDefaultDatagridFilter($datagridMapper, 'modelRegionProjects');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $this->addAddressListFields($listMapper);
        $listMapper->add('url', 'url');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name');
        $this->addAddressShowFields($showMapper);
        $showMapper->add('url', 'url');
        $this->addModelRegionProjectsShowFields($showMapper);
    }
}
