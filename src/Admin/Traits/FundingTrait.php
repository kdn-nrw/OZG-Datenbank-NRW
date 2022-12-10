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

use App\Admin\FundingAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait FundingTrait
{
    protected function addFundingsFormFields(FormMapper $form)
    {
        $form->add('fundings', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addFundingsListFields(ListMapper $list)
    {
        $list
            ->add('fundings', null,[
                'admin_code' => FundingAdmin::class,
            ]);
    }

    /**
     * Add the funding show fields
     * @param ShowMapper $show
     */
    public function addFundingsShowFields(ShowMapper $show)
    {
        $show
            ->add('fundings', null,[
                'admin_code' => FundingAdmin::class,
            ]);
    }
}