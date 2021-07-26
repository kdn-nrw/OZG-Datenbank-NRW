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
use App\Form\Type\CommuneType;
use App\Form\Type\OnboardingContactType;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ServiceAccountAdmin extends AbstractOnboardingAdmin
{
    protected $baseRoutePattern = 'onboarding/servicekonto';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->configureFormGroups($formMapper);
        $this->addMandatorFormFields($formMapper);
        if ($this->isGranted('ALL')) {
            $this->addMandatorAccountFormFields($formMapper);
        }
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
                'label' => 'app.service_account.groups.admin_account',
                'class' => 'col-md-12',
            ])
            ->end()
            ->with('mandator_email', [
                'label' => 'app.service_account.groups.mandator_email',
                'class' => 'col-md-12',
                'description' => 'app.service_account.groups.mandator_email_description',
            ])
            ->end()
            ->end();
        if ($this->isGranted('ALL')) {
            $formMapper
                ->tab('Mandator', [
                    'label' => 'app.service_account.tabs.mandator',
                    'tab' => true,
                ])
                /*->with('payment_provider', [
                    'label' => false,//'app.service_account.groups.payment_provider',
                    'class' => 'col-md-12',
                ])
                ->end()*/
                ->with('account', [
                    'label' => false,//'app.service_account.groups.account',
                    'class' => 'col-md-12',
                ])
                ->end()
                ->with('account2', [
                    'label' => false,//'app.service_account.groups.account',
                    'class' => 'col-md-12',
                ])
                ->end()
                ->end();
        }
    }

    protected function addMandatorFormFields(FormMapper $formMapper)
    {
        $formMapper->tab('General');

        $formMapper
            ->with('general')
            ->add('commune', CommuneType::class, [
                'label' => false,
                //'required' => true,
                'disabled' => true,
                'required' => false
            ], [
                'admin_code' => CommuneAdmin::class,
            ])
            ->add('street', TextType::class, [
                'label' => 'app.service_account.entity.street',
                'required' => false,
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'app.service_account.entity.zip_code',
                'required' => false,
            ])
            ->add('town', TextType::class, [
                'label' => 'app.service_account.entity.town',
                'required' => false,
            ]);

        $formMapper->end();
        $formMapper
            ->with('admin_account')
            ->add('paymentUser', OnboardingContactType::class, [
                'label' => false,
                'required' => false,
                'parent_admin' => $this,
                'show_contact_type' => false,
                'enable_external_user' => false,
                'enable_mobile_number' => false,
                'enable_phone_number' => false,
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
            ])
            ->add('groupEmail', EmailType::class, [
                'label' => 'app.service_account.entity.group_email',
                'required' => false,
            ]);

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
            ->with('account2')
            ->add('answerUrl2', UrlType::class, [
                'required' => false,
            ])
            ->add('clientId2', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'app.service_account.entity.client_id2_placeholder',
                ],
            ])
            ->add('clientPassword2', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'app.service_account.entity.client_password2_placeholder',
                ],
            ])
            ->end()
            ->end();
    }
}
