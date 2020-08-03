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

namespace App\Admin\Traits;

use App\Admin\ModelRegionAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Trait ModelRegionTrait
 * @package App\Admin\Traits
 * @property array $customShowFields
 */
trait ModelRegionTrait
{
    protected function addModelRegionsFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('modelRegions', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ], [
            'admin_code' => ModelRegionAdmin::class,
        ]);
    }

    protected function addModelRegionsDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('modelRegions',
            null, [
                'admin_code' => ModelRegionAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addModelRegionsListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('modelRegions', null,[
                'admin_code' => ModelRegionAdmin::class,
            ]);
    }

    public function addModelRegionsShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('modelRegions', null,[
                'admin_code' => ModelRegionAdmin::class,
            ]);
        //$this->customShowFields[] = 'modelRegions';
    }
}