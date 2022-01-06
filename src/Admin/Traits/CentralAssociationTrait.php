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

use App\Admin\StateGroup\CentralAssociationAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Trait CentralAssociationTrait
 * @package App\Admin\Traits
 */
trait CentralAssociationTrait
{
    protected function addCentralAssociationsFormFields(FormMapper $form): void
    {
        $form->add('centralAssociations', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addCentralAssociationsListFields(ListMapper $list): void
    {
        $list
            ->add('centralAssociations', null,[
                'admin_code' => CentralAssociationAdmin::class,
            ]);
    }

    public function addCentralAssociationsShowFields(ShowMapper $show): void
    {
        $show
            ->add('centralAssociations', null,[
                'admin_code' => CentralAssociationAdmin::class,
            ]);
    }
}