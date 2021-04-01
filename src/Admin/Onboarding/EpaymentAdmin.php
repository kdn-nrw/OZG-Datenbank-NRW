<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Onboarding;

use App\Form\Type\OnboardingContactType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class EpaymentAdmin extends AbstractOnboardingAdmin
{
    protected $baseRoutePattern = 'onboarding/epaybl';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('app.epayment.groups.general', ['class' => 'col-md-12']);
        $formMapper
            ->add('communeName', TextType::class, [
                //'required' => true,
                'disabled' => true,
                'required' => false,
            ])
            ->add('officialCommuneKey', TextType::class, [
                //'required' => true,
                'disabled' => true,
                'required' => false,
            ]);
        $formMapper
            ->end()
            ->with('app.epayment.groups.payment_provider', ['class' => 'col-md-12']);
        $formMapper
            ->add('paymentProviderAccountId', TextType::class, [
                'required' => false,
            ])
            ->add('paymentProvider', UrlType::class, [
                'required' => false,
                'disabled' => true,
            ]);
        $formMapper
            ->end()
            ->with('app.epayment.groups.user', ['class' => 'col-md-12']);
        $formMapper
            ->add('paymentUser', OnboardingContactType::class, [
                'label' => false,
                'required' => false,
                'parent_admin' => $this,
                'show_contact_type' => false,
                'enable_external_user' => true,
                'enable_mobile_number' => true,
            ]);
        $formMapper
            ->end()
            ->with('app.epayment.groups.mandator_email', [
                'class' => 'col-md-12',
                'description' => 'app.epayment.groups.mandator_email_description',
            ]);
        $formMapper
            ->add('mandatorEmail', EmailType::class, [
                'required' => false,
                'help' => 'bevorzugt eine Funktionsadresse',
            ]);
        $formMapper
            ->end()
            ->with('app.epayment.groups.activation_system', [
                'class' => 'col-md-12',
                'description' => 'app.epayment.groups.activation_system_description',
            ]);
        $formMapper
            ->add('testIpAddress', TextType::class, [
                'required' => false,
                'help' => '<a href="https://www.wieistmeineip.de/" target="_blank">Aktuelle IP-Adresse?</a>',
            ]);
        $formMapper
            ->end();
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('communeName')
            ->add('officialCommuneKey')
            ->add('paymentProviderAccountId')
            ->add('paymentProvider')
            ->add('paymentUser')
            ->add('mandatorEmail')
            ->add('testIpAddress');
    }
}
