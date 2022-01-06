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
use App\Entity\Onboarding\Epayment;
use App\Form\Type\CommuneType;
use App\Form\Type\EpaymentProjectType;
use App\Form\Type\OnboardingContactType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EpaymentAdmin extends AbstractOnboardingAdmin
{
    use ServiceProviderTrait;

    protected $baseRoutePattern = 'onboarding/epaybl';

    protected function configureFormGroups(FormMapper $form)
    {
        $form
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
        $form
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
            ->with('account', [
                'label' => false,//'app.epayment.groups.account',
                'class' => 'col-md-12',
            ])
            ->end()
            ->with('project_group', [
                'label' => false,//'app.epayment.groups.project_group',
                'class' => 'col-md-12 box-collection-table four-col box-collection-epayment-projects',
                // Show text after account group!
                'description' => 'app.epayment.groups.account_description',
            ])
            ->end()
            ->end();
        $form
            ->with('Manager', [
                'label' => 'app.epayment.tabs.manager',
                'tab' => true,
                'description' => 'app.epayment.tabs.manager_description',
            ])
            ->with('manager_info', [
                'label' => false,
                'class' => 'col-md-12',
                //'description' => 'app.epayment.groups.activation_system_description',
            ])
            ->end()
            ->end();
        $form
            ->with('Testsystem', [
                'label' => 'app.epayment.groups.activation_system',
                'tab' => true,
                'description' => 'app.epayment.groups.activation_system_description',
            ])
            ->with('activation_system', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
        $form
            ->with('Services', [
                'label' => 'app.epayment.tabs.services',
                'tab' => true,
            ])
            ->with('epayment_services', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->end()
            ->end();
    }

    protected function addMandatorFormFields(FormMapper $form)
    {
        $form->tab('Mandator');
        $form
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
            ]);

        $this->addDataCompletenessConfirmedField($form);
        $this->addServiceProvidersFormFields($form, 'paymentOperator', 'paymentProvider');
        $form->end();
        $form
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
        $form
            ->with('mandator_email')
            ->add('mandatorEmail', EmailType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'bevorzugt eine Funktionsadresse (z.B. epaybl@musterkommune.de)',
                ],
                //'help' => 'bevorzugt eine Funktionsadresse',
            ]);

        $this->addGroupEmailFormField($form, true);
        $form->end();
        $form
            ->end();
    }

    protected function addManagerFormFields(FormMapper $form)
    {
        $form
            ->tab('Manager')
            ->with('manager_info')
            /*
            ->add('clientNumberIntegration', TextType::class, [
                'required' => false,
            ])
            ->add('clientNumberProduction', TextType::class, [
                'required' => false,
            ])
            ->add('managerNumber', TextType::class, [
                'required' => false,
            ])*/
            ->add('cashRegisterPersonalAccountNumber', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'kann von ePayBL zur Verfügung gestellt werden',
                ],
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
            ->add('contentFirstAccountAssignmentInformation', TextareaType::class, [
                'required' => false,
            ])
            ->add('lengthSecondAccountAssignmentInformation', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Bspw. 12 Zeichen',
                ],
            ])
            ->add('contentSecondAccountAssignmentInformation', TextareaType::class, [
                'required' => false,
            ])
            /*
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
            ])*/
            ->add('managerNo', TextType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('applicationName', TextType::class, [
                'required' => false,
                'disabled' => true
            ])
            ->add('xFinanceFileRequired', ChoiceFieldMaskType::class, [
                'choices' => [
                    'app.epayment.entity.x_finance_file_required_choices.no' => false,
                    'app.epayment.entity.x_finance_file_required_choices.yes' => true,
                ],
                'map' => [
                    false => [],
                    true => ['xFinanceFileDays'],
                ],
                'required' => false,
            ])
            ->add('xFinanceFileDays', ChoiceType::class, [
                'choices' => array_flip(Epayment::getDayChoices()),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choice_translation_domain' => false,
            ]);
        /*
        ->add('testIpAddress', TextType::class, [
            'required' => false,
            'help' => '<a href="https://www.wieistmeineip.de/" target="_blank">Aktuelle IP-Adresse?</a>',
        ])*/
        $form
            ->end()
            ->end();
    }

    protected function configureFormFields(FormMapper $form)
    {
        $this->configureFormGroups($form);
        $this->addMandatorFormFields($form);
        $form
            ->tab('Provider');
        /*->with('payment_provider')
        ->add('paymentProvider', UrlType::class, [
            'required' => false,
            'disabled' => true,
        ])
        ->end()*/
        $form
            ->with('account');
        $form
            ->add('paymentProviderAccountId', TextType::class, [
                'required' => false,
            ])
            ->end()
            ->with('project_group')
            ->add('projects', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'label' => false,
                'entry_type' => EpaymentProjectType::class,
                'entry_options' => [
                    'parent_admin' => $this,
                ],
            ])
            ->end()
            ->end();
        $this->addManagerFormFields($form);
        $form
            ->with('Testsystem', [
                'label' => 'app.epayment.groups.activation_system',
                'tab' => true,
                'description' => 'app.epayment.groups.activation_system_description',
            ])
            ->with('activation_system', [
                'label' => false,
                'class' => 'col-md-12',
            ])
            ->add('testIpAddress', TextType::class, [
                'required' => false,
                'help' => '<a href="https://www.wieistmeineip.de/" target="_blank">Aktuelle IP-Adresse?</a>',
            ])
            ->end()
            ->end();
        /** @var Epayment|null $subject */
        $subject = $this->getSubject();
        $form
            ->tab('Services')
            ->with('epayment_services')
            ->add('epaymentServices', CollectionType::class, [
                'label' => false,
                'type_options' => [
                    'delete' => false,
                ],
                'btn_add' => false,
                'by_reference' => false,
            ], [
                'edit' => 'inline',
                'inline' => 'natural',
                'sortable' => 'position',
                'ba_custom_exclude_fields' => ['epayment'],
                'ba_disable_required_fields' => null !== $subject && null !== $subject->getId(),
            ])
            ->end()
            ->end();
    }
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        parent::configureDatagridFilters($filter);
        $this->addDefaultDatagridFilter($filter, 'paymentOperator');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('commune', null, [
                'admin_code' => CommuneAdmin::class,
            ]);
        $this->addServiceProvidersListFields($list, 'paymentOperator');
        $list
            ->add('modifiedAt');
        $this->addListStatusField($list);
        $this->addOnboardingDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('communeName')
            ->add('officialCommuneKey')
            ->add('paymentProviderAccountId')
            ->add('paymentProvider')
            ->add('paymentUser')
            ->add('projects')
            ->add('mandatorEmail')
            ->add('groupEmail')
            ->add('testIpAddress')
            ->add('clientNumberIntegration')
            ->add('clientNumberProduction')
            ->add('managerNumber')
            ->add('cashRegisterPersonalAccountNumber')
            ->add('lengthReceiptNumber')
            ->add('cashRegisterCheckProcedureStatus')
            ->add('lengthFirstAccountAssignmentInformation')
            ->add('contentFirstAccountAssignmentInformation')
            ->add('lengthSecondAccountAssignmentInformation')
            ->add('contentSecondAccountAssignmentInformation')
            /*->add('budgetOffice')
            ->add('objectNumber')
            ->add('indicatorDunningProcedure')
            ->add('bookingText')
            ->add('descriptionOfTheBookingList')*/
            ->add('managerNo')
            ->add('applicationName')
            ->add('xFinanceFileRequired')
            ->add('xFinanceFileDays', TemplateRegistryInterface::TYPE_CHOICE, [
                'editable' => false,
                'choices' => Epayment::getDayChoices(),
                'catalogue' => 'messages',
            ])
            ->add('epaymentServices');
    }
}
