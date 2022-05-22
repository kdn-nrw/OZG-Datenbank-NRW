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

use App\Admin\AbstractAppAdmin;
use App\Admin\Base\AuditedEntityAdminInterface;
use App\Admin\Base\AuditedEntityAdminTrait;
use App\Admin\StateGroup\CommuneAdmin;
use App\Admin\Traits\AddressTrait;
use App\Entity\Onboarding\Contact;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ContactAdmin extends AbstractAppAdmin implements AuditedEntityAdminInterface
{
    use AuditedEntityAdminTrait;
    use AddressTrait;
    protected $baseRoutePattern = 'onboarding/contact';

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('app.contact.groups.person_data', ['class' => 'col-md-6'])
            ->add('gender', ChoiceType::class, [
                'choices' => array_flip(Contact::$genderTypeChoices),
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'required' => false,
            ])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class);
        $form
            ->end()
            ->with('app.contact.groups.address_data', ['class' => 'col-md-6']);
        $this->addAddressFormFields($form);
        $form->end();
        $form
            ->end();
        $form->with('app.contact.groups.contact', ['class' => 'col-md-6'])
            ->add('email', EmailType::class, [
                'required' => false,
            ]);
        if (!$this->isExcludedFormField('phoneNumber')) {
            $form
                ->add('phoneNumber', TextType::class, [
                    'required' => false,
                ]);
        }
        if (!$this->isExcludedFormField('mobileNumber')) {
            $form
                ->add('mobileNumber', TextType::class, [
                    'required' => false,
                ]);
        }
        if (!$this->isExcludedFormField('contactType')) {
            $form->add('contactType', ChoiceType::class, [
                'label' => 'app.contact.entity.contact_type',
                'choices' => Contact::$contactTypeChoices,
                'required' => true,
            ]);
        }
        if (!$this->isExcludedFormField('externalUserName')) {
            $form->add('externalUserName', TextType::class, [
                'label' => 'app.epayment.entity.payment_user_username',
                'required'    => false,
            ]);
        }
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('firstName');
        $filter->add('lastName');
        $filter->add('email');
        $this->addDefaultDatagridFilter($filter, 'commune');
        $this->addAddressDatagridFilters($filter);
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('gender', 'choice', [
                'editable' => false,
                'choices' => Contact::$genderTypeChoices,
                'catalogue' => 'messages',
            ])
            ->add('title')
            ->add('lastName')
            ->add('firstName')
            ->add('email')
            ->add('commune', null, [
                'admin_code' => CommuneAdmin::class,
            ]);
        $this->addAddressListFields($list);

        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            /*->add('gender', 'choice', [
                'editable' => false,
                'choices' => Contact::$genderTypeChoices,
                'catalogue' => 'messages',
            ])*/
            ->add('title')
            ->add('lastName')
            ->add('firstName')
            ->add('email');
        $this->addAddressShowFields($show);
        $show/*->add('commune', null, [
                'admin_code' => CommuneAdmin::class,
            ])*/
            ->add('phoneNumber')
            ->add('mobileNumber')
            ->add('externalUserName', null, [
                'label' => 'app.epayment.entity.payment_user_username',
            ]);
    }
}
