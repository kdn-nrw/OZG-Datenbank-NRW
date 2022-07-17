<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Traits;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait PaymentPlatformTrait
{
    protected function addPaymentPlatformsFormFields(FormMapper $form)
    {
        $form
            ->add('paymentPlatforms', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ]
            );
    }

    protected function addPaymentPlatformsListFields(ListMapper $list)
    {
        $list
            ->add('paymentPlatforms');
    }

    /**
     * @inheritdoc
     */
    public function addPaymentPlatformsShowFields(ShowMapper $show)
    {
        $show
            ->add('paymentPlatforms');
    }
}