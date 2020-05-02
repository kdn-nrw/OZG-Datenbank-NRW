<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\CategoryTrait;
use App\Entity\Contact;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ContactAdmin extends AbstractAppAdmin
{
    use CategoryTrait;
    use AddressTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $hideFields = $this->getFormHideFields();
        $formMapper
            ->with('app.contact.groups.person_data', ['class' => 'col-md-6'])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'app.contact.entity.gender_choices.male' => Contact::GENDER_MALE,
                    'app.contact.entity.gender_choices.female' => Contact::GENDER_FEMALE,
                    'app.contact.entity.gender_choices.other' => Contact::GENDER_OTHER,
                    'app.contact.entity.gender_choices.unknown' => Contact::GENDER_UNKNOWN,
                ],
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'required' => false,
            ])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->end()
            ->with('app.contact.groups.address_data', ['class' => 'col-md-6']);
        $this->addAddressFormFields($formMapper);
        $formMapper->end();
        $formMapper
            ->end()
            ->with('app.contact.groups.organisation', ['class' => 'col-md-6']);
        if (!in_array('organisationEntity', $hideFields, false)) {
            $formMapper->add('organisationEntity', ModelType::class, [
                    'label' => 'app.contact.entity.organisation_entity_form',
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ]);
        }
        if (!in_array('organisationEntity', $hideFields, false)) {
            $formMapper->add('organisation', TextType::class, [
                    'label' => 'app.contact.entity.organisation_form',
                    'required' => false,
                ]);
        }
        $formMapper
            ->add('department', TextType::class, [
                'required' => false,
            ])
            ->add('position', TextType::class, [
                'required' => false,
            ]);
        $this->addCategoriesFormFields($formMapper);
        $formMapper->end();
        $formMapper->with('app.contact.groups.contact', ['class' => 'col-md-6'])
            ->add('email', EmailType::class, [
                'required' => false,
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
            ])
            ->add('faxNumber', TextType::class, [
                'required' => false,
            ])
            ->add('mobileNumber', TextType::class, [
                'required' => false,
            ]);
        if (!in_array('contactType', $hideFields, false)) {
            $formMapper->add('contactType', ChoiceType::class, [
                'choices' => [
                    'app.contact.entity.contact_type_choices.default' => Contact::CONTACT_TYPE_DEFAULT,
                    'app.contact.entity.contact_type_choices.cms_address' => Contact::CONTACT_TYPE_IMPORT_CMS,
                ],
                'required' => true,
            ]);
        }
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addFullTextDatagridFilter($datagridMapper);
        $datagridMapper->add('firstName');
        $datagridMapper->add('lastName');
        $datagridMapper->add('email');
        $datagridMapper->add('organisation');
        $this->addCategoriesDatagridFilters($datagridMapper);
        $this->addAddressDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('gender', 'choice', [
                'editable' => false,
                'choices' => [
                    Contact::GENDER_MALE => 'app.contact.entity.gender_choices.male',
                    Contact::GENDER_FEMALE => 'app.contact.entity.gender_choices.female',
                    Contact::GENDER_OTHER => 'app.contact.entity.gender_choices.other',
                ],
                'catalogue' => 'messages',
            ])
            ->add('title')
            ->add('lastName')
            ->add('firstName')
            ->add('email')
            ->add('organisation');
        $this->addAddressListFields($listMapper);

        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('gender', 'choice', [
                'editable' => false,
                'choices' => [
                    Contact::GENDER_MALE => 'app.contact.entity.gender_choices.male',
                    Contact::GENDER_FEMALE => 'app.contact.entity.gender_choices.female',
                    Contact::GENDER_OTHER => 'app.contact.entity.gender_choices.other',
                ],
                'catalogue' => 'messages',
            ])
            ->add('title')
            ->add('lastName')
            ->add('firstName')
            ->add('email');
        $this->addAddressShowFields($showMapper);
        $showMapper->add('organisation')
            ->add('department')
            ->add('position')
            ->add('phoneNumber')
            ->add('faxNumber')
            ->add('mobileNumber');
        $this->addCategoriesShowFields($showMapper);
    }
}
