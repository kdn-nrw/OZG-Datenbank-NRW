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

namespace App\Admin\Onboarding;

use App\Admin\Base\AuditedEntityAdminInterface;
use App\Admin\Base\AuditedEntityAdminTrait;
use App\Admin\StateGroup\CommuneAdmin;
use App\Admin\Traits\ServiceProviderTrait;
use App\Entity\Onboarding\PmPayment;
use App\Form\Type\CommuneType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Valid;

class PmPaymentAdmin extends AbstractOnboardingAdmin implements AuditedEntityAdminInterface
{
    use AuditedEntityAdminTrait;
    use ServiceProviderTrait;

    protected $baseRoutePattern = 'onboarding/pm-payment';

    protected function configureFormGroups(FormMapper $form)
    {
        $form
            ->with('Mandator', [
                'label' => 'app.pm_payment.tabs.mandator',
                'tab' => true,
            ])
            ->with('general', [
                'label' => 'app.pm_payment.groups.general',
                'class' => 'col-md-12',
                'description' => 'app.pm_payment.groups.general_description',
            ])
            ->end()
            ->with('systems', [
                'label' => 'app.pm_payment.groups.systems',
                'class' => 'col-md-12 pm-payment-group-systems',
            ])
            ->end()
            ->end();
        $form
            ->with('Services', [
                'label' => 'app.pm_payment.tabs.services',
                'tab' => true,
            ])
            ->with('pm_payment_services', [
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
                'label' => 'app.pm_payment.entity.street',
                'required' => false,
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'app.pm_payment.entity.zip_code',
                'required' => false,
            ])
            ->add('town', TextType::class, [
                'label' => 'app.pm_payment.entity.town',
                'required' => false,
            ]);

        $this->addDataCompletenessConfirmedField($form);
        $form->end();
        $form
            ->with('systems')
            ->add('endpointSystemTest', TextType::class, [
                'label' => 'app.pm_payment.entity.endpoint',
                'row_attr' => ['class' => 'form-group-endpoint row-1'],
                'help' => 'app.pm_payment.entity.system_test',
            ])
            ->add('passwordSystemTest', TextType::class, [
                'label' => 'app.pm_payment.entity.password',
                'row_attr' => ['class' => 'form-group-password row-1'],
                'help' => 'app.pm_payment.entity.system_test',
            ])
            ->add('endpointSystemProduction', TextType::class, [
                'label' => 'app.pm_payment.entity.endpoint',
                'row_attr' => ['class' => 'form-group-endpoint row-2'],
                'help' => 'app.pm_payment.entity.system_production',
            ])
            ->add('passwordSystemProduction', TextType::class, [
                'label' => 'app.pm_payment.entity.password',
                'row_attr' => ['class' => 'form-group-password row-2'],
                'help' => 'app.pm_payment.entity.system_production',
            ])
            ->end();
        $form->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $this->configureFormGroups($form);
        $this->addMandatorFormFields($form);
        /** @var PmPayment|null $subject */
        $subject = $this->getSubject();
        $form
            ->tab('Services')
            ->with('pm_payment_services')
            ->add('pmPaymentServices', CollectionType::class, [
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
                'ba_custom_exclude_fields' => ['pmPayment'],
                'ba_disable_required_fields' => null !== $subject && null !== $subject->getId(),
            ])
            ->end()
            ->end();
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('pmPaymentServices')
            ->addConstraint(new Valid())
            ->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('commune', null, [
                'admin_code' => CommuneAdmin::class,
            ]);
        $list
            ->add('modifiedAt');
        $this->addListStatusField($list);
        $this->addOnboardingDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('communeName')
            ->add('street')
            ->add('zipCode')
            ->add('town')
            ->add('officialCommuneKey')
            ->add('endpointSystemTest')
            ->add('passwordSystemTest')
            ->add('endpointSystemProduction')
            ->add('passwordSystemProduction');

        $show
            ->add('pmPaymentServices');
    }
}
