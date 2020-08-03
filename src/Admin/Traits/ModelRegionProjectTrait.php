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

use App\Admin\ModelRegionProjectAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Trait ModelRegionProjectTrait
 * @package App\Admin\Traits
 * @property array $customShowFields
 */
trait ModelRegionProjectTrait
{
    protected function addModelRegionProjectsFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('modelRegionProjects', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ], [
            'admin_code' => ModelRegionProjectAdmin::class,
        ]);
    }

    protected function addModelRegionProjectsDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('modelRegionProjects',
            null, [
                'admin_code' => ModelRegionProjectAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addModelRegionProjectsListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('modelRegionProjects', null,[
                'admin_code' => ModelRegionProjectAdmin::class,
            ]);
    }

    public function addModelRegionProjectsShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('modelRegionProjects', null,[
                'admin_code' => ModelRegionProjectAdmin::class,
            ]);
        //$this->customShowFields[] = 'modelRegionProjects';
    }
}