<?php

namespace App\Admin;

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\CategoryTrait;
use App\Entity\Contact;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                        'app.contact.entity.gender_choices.male'  => Contact::GENDER_MALE,
                        'app.contact.entity.gender_choices.female' => Contact::GENDER_FEMALE,
                        'app.contact.entity.gender_choices.other' => Contact::GENDER_OTHER,
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
        if (!in_array('contactType', $hideFields)) {
        $formMapper
            ->with('app.contact.groups.type_data', ['class' => 'col-md-6'])
                ->add('contactType', ChoiceFieldMaskType::class, [
                    'choices' => [
                        'app.contact.entity.contact_type_default' => Contact::CONTACT_TYPE_DEFAULT,
                        'app.contact.entity.contact_type_commune' => Contact::CONTACT_TYPE_COMMUNE,
                        'app.contact.entity.contact_type_service_provider' => Contact::CONTACT_TYPE_SERVICE_PROVIDER,
                        'app.contact.entity.contact_type_ministry_state' => Contact::CONTACT_TYPE_MINISTRY_STATE,
                    ],
                    'map' => [
                        Contact::CONTACT_TYPE_DEFAULT => [],
                        Contact::CONTACT_TYPE_COMMUNE => ['commune'],
                        Contact::CONTACT_TYPE_SERVICE_PROVIDER => ['serviceProvider'],
                        Contact::CONTACT_TYPE_MINISTRY_STATE => ['ministryState'],
                    ],
                    'required' => true,
                ])
                ->add('commune', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ])
                ->add('serviceProvider', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ])
                ->add('ministryState', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ]);
            $formMapper->end();
        }
        $formMapper
            ->end()
            ->with('app.contact.groups.organisation', ['class' => 'col-md-6'])
                ->add('organisation', TextType::class, [
                    'required' => false,
                ])
                ->add('department', TextType::class, [
                    'required' => false,
                ])
                ->add('position', TextType::class, [
                    'required' => false,
                ]);
            $this->addCategoriesFormFields($formMapper);
            $formMapper->end();
            $formMapper->with('app.contact.groups.contact', ['class' => 'col-md-6'])
                ->add('email', TextType::class, [
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
                ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('lastName');
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
                    Contact::GENDER_MALE  => 'app.contact.entity.gender_choices.male',
                    Contact::GENDER_FEMALE  => 'app.contact.entity.gender_choices.female',
                    Contact::GENDER_OTHER  => 'app.contact.entity.gender_choices.other',
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
                    Contact::GENDER_MALE  => 'app.contact.entity.gender_choices.male',
                    Contact::GENDER_FEMALE  => 'app.contact.entity.gender_choices.female',
                    Contact::GENDER_OTHER  => 'app.contact.entity.gender_choices.other',
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
