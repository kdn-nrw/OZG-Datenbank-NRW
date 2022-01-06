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

use App\Admin\LaboratoryAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait LaboratoryTrait
{
    protected function addLaboratoriesFormFields(FormMapper $form)
    {
        $form->add('laboratories', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addLaboratoriesListFields(ListMapper $list)
    {
        $list
            ->add('laboratories', null,[
                'admin_code' => LaboratoryAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addLaboratoriesShowFields(ShowMapper $show)
    {
        $show
            ->add('laboratories', null,[
                'admin_code' => LaboratoryAdmin::class,
            ]);
    }
}