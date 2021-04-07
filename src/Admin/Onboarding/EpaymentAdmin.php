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
use App\Form\Type\EpaymentProjectType;
use App\Form\Type\OnboardingContactType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EpaymentAdmin extends AbstractOnboardingAdmin
{
    protected $baseRoutePattern = 'onboarding/epaybl';

    protected function configureFormGroups(FormMapper $formMapper)
    {
        $formMapper
            ->with('Mandator', [
                'label' => 'app.epayment.tabs.mandator',
                'tab' => true,
            ])
                ->with('general', [
                    'label' => 'app.epayment.groups.general',
                    'class' => 'col-md-12',
                    'description' => 'app.epayment.groups.general_description',
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
            ->with('Provider', [
                'label' => 'app.epayment.tabs.provider',
                'tab' => true,
                'description' => 'app.epayment.tabs.provider_description',
            ])
                /*->with('payment_provider', [
                    'label' => false,//'app.epayment.groups.payment_provider',
                    'class' => 'col-md-12',
                ])
                ->end()*/
                ->with('project_group', [
                    'label' => false,//'app.epayment.groups.project_group',
                    'class' => 'col-md-12 box-collection-table four-col box-collection-epayment-projects',
                ])
                ->end()
            ->end();
        $formMapper
            ->with('Manager', [
                'label' => 'app.epayment.tabs.manager',
                'tab' => true,
                'description' => 'app.epayment.tabs.manager_description',
            ])
                ->with('app.epayment.groups.activation_system', [
                    'class' => 'col-md-12',
                    'description' => 'app.epayment.groups.activation_system_description',
                ])
                ->end()
            ->end();
    }

    protected function addMandatorFormFields(FormMapper $formMapper)
    {
        $formMapper->tab('Mandator');
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
            ])
            ->end();
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
            ])
            ->end();
        $formMapper
            ->end();
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->configureFormGroups($formMapper);
        $this->addMandatorFormFields($formMapper);
        $formMapper
            ->tab('Provider')
            /*->with('payment_provider')
            ->add('paymentProviderAccountId', TextType::class, [
                'required' => false,
            ])
            ->add('paymentProvider', UrlType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->end()*/
            ->with('project_group')
            ->add('projects', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'label' => false,
                'entry_type' => EpaymentProjectType::class,
                'entry_options' => [
                    'parent_admin' => $this,
                ],
                'row_attr' => [
                    'class' => 'form-group form-group-head',
                ],
            ])
            ->end()
            ->end();
        $formMapper
            ->tab('Manager')
            ->with('app.epayment.groups.activation_system')
            ->add('clientNumberIntegration', TextType::class, [
                'required' => false,
            ])
            ->add('clientNumberProduction', TextType::class, [
                'required' => false,
            ])
            ->add('managerNumber', TextType::class, [
                'required' => false,
            ])
            ->add('budgetOffice', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Finanzposition',
                ],
            ])
            ->add('objectNumber', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Sachkonto',
                ],
            ])
            ->add('cashRegisterPersonalAccountNumber', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'kann von ePayBL zur Verfügung gestellt werden',
                ],
            ])
            ->add('indicatorDunningProcedure', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => '11',
                ],
            ])
            ->add('bookingText', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Mahngebühr',
                ],
            ])
            ->add('descriptionOfTheBookingList', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Antrag Ehefähigkeitszeugnis',
                ],
            ])
            ->add('managerNo', TextType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('applicationName', TextType::class, [
                'required' => false,
                'disabled' => true
            ])
            ->add('lengthReceiptNumber', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Bspw. 12 Zeichen',
                ],
            ])
            ->add('cashRegisterCheckProcedureStatus', CheckboxType::class, [
                'required' => false,
            ])
            ->add('lengthFirstAccountAssignmentInformation', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Bspw. 12 Zeichen',
                ],
            ])
            ->add('lengthSecondAccountAssignmentInformation', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Bspw. 12 Zeichen',
                ],
            ]);
            /*
            ->add('testIpAddress', TextType::class, [
                'required' => false,
                'help' => '<a href="https://www.wieistmeineip.de/" target="_blank">Aktuelle IP-Adresse?</a>',
            ])*/
        $formMapper
            ->end()
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
