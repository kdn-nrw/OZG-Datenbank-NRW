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

namespace App\Admin\Traits;

use App\Admin\SpecializedProcedureAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait SpecializedProcedureTrait
{
    protected function addSpecializedProceduresFormFields(FormMapper $form, string $propertyName = 'specializedProcedures')
    {
        $form->add($propertyName, ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addSpecializedProceduresListFields(ListMapper $list, string $propertyName = 'specializedProcedures')
    {
        $list
            ->add($propertyName, null,[
                'admin_code' => SpecializedProcedureAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addSpecializedProceduresShowFields(ShowMapper $show, string $propertyName = 'specializedProcedures')
    {
        $show
            ->add($propertyName, null,[
                'admin_code' => SpecializedProcedureAdmin::class,
            ]);
    }
}