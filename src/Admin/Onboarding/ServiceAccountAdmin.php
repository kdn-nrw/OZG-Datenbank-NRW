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


use App\Admin\StateGroup\CommuneAdmin;
use App\Admin\Traits\ServiceProviderTrait;
use App\Entity\Onboarding\ServiceAccount;
use App\Form\Type\CommuneType;
use App\Form\Type\OnboardingContactType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ServiceAccountAdmin extends AbstractOnboardingAdmin
{
    use ServiceProviderTrait;

    protected $baseRoutePattern = 'onboarding/servicekonto';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->configureFormGroups($formMapper);
        $this->addMandatorFormFields($formMapper);
        $this->addMandatorAccountFormFields($formMapper);
    }

    protected function configureFormGroups(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General', [
                'label' => 'app.service_account.tabs.general',
                'description' => 'app.service_account.tabs.general_description',
            ])
            ->with('general', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->with('admin_account', [
                'label' => 'app.epayment.groups.admin_account',
                'class' => 'col-md-12',
            ])
            ->end()
            ->with('mandator_email', [
                'label' => 'app.epayment.groups.mandator_email',
                'class' => 'col-md-12',
                'description' => 'app.epayment.groups.mandator_email_description',
            ])
            ->end()
            ->end();
        $formMapper
            ->tab('Mandator', [
                'label' => 'app.service_account.tabs.mandator',
                'tab' => true,
            ])
            /*->with('payment_provider', [
                'label' => false,//'app.epayment.groups.payment_provider',
                'class' => 'col-md-12',
            ])
            ->end()*/
            ->with('account', [
                'label' => false,//'app.epayment.groups.account',
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
    }

    protected function addMandatorFormFields(FormMapper $formMapper)
    {
        $formMapper->tab('General');

        $formMapper
            ->with('general')
            ->add('accountMandatorState', ChoiceFieldMaskType::class, [
                'choices' => ServiceAccount::$accountMandatorChoices,
                'map' => [
                    1 => [],
                    2 => ['commune', 'street', 'zipCode', 'town', 'paymentOperator', 'paymentUser', 'mandatorEmail', 'groupEmail'],
                ],
                'required' => true,
            ])
            ->add('commune', CommuneType::class, [
                'label' => false,
                //'required' => true,
                'disabled' => true,
                'required' => false
            ], [
                'admin_code' => CommuneAdmin::class,
            ])
            ->add('street', TextType::class, [
                'label' => 'app.epayment.entity.street',
                'required' => false,
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'app.epayment.entity.zip_code',
                'required' => false,
            ])
            ->add('town', TextType::class, [
                'label' => 'app.epayment.entity.town',
                'required' => false,
            ]);

        $this->addServiceProvidersFormFields($formMapper, 'paymentOperator', 'paymentProvider');
        $formMapper->end();
        $formMapper
            ->with('admin_account')
            ->add('paymentUser', OnboardingContactType::class, [
                'label' => false,
                'required' => false,
                'parent_admin' => $this,
                'show_contact_type' => false,
                'enable_external_user' => true,
                'enable_mobile_number' => true,
            ])
            ->end();
        $formMapper
            ->with('mandator_email')
            ->add('mandatorEmail', EmailType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'bevorzugt eine Funktionsadresse (z.B. epaybl@musterkommune.de)',
                ],
                //'help' => 'bevorzugt eine Funktionsadresse',
            ]);

        $this->addGroupEmailFormField($formMapper, true);
        $formMapper->end();
        $formMapper
            ->end();
    }

    protected function addMandatorAccountFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Mandator')
            ->with('account')
            ->add('answerUrl1', UrlType::class, [
                'required' => false,
            ])
            ->add('answerUrl2', UrlType::class, [
                'required' => false,
            ])
            ->add('clientId', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'app.service_account.entity.client_id_placeholder',
                ],
            ])
            ->add('clientPassword', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'app.service_account.entity.client_password_placeholder',
                ],
            ])
            ->end()
            ->end();
    }
}
