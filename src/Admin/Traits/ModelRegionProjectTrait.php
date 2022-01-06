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

use App\Admin\ModelRegion\ModelRegionProjectAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Trait ModelRegionProjectTrait
 * @package App\Admin\Traits
 */
trait ModelRegionProjectTrait
{
    protected function addModelRegionProjectsFormFields(FormMapper $form): void
    {
        $form->add('modelRegionProjects', ModelType::class, [
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

    protected function addModelRegionProjectsListFields(ListMapper $list): void
    {
        $list
            ->add('modelRegionProjects', null,[
                'admin_code' => ModelRegionProjectAdmin::class,
            ]);
    }

    public function addModelRegionProjectsShowFields(ShowMapper $show): void
    {
        $show
            ->add('modelRegionProjects', null,[
                'admin_code' => ModelRegionProjectAdmin::class,
            ]);
    }
}