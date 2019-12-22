<?php

namespace App\Admin;

use App\Admin\Traits\CategoryTrait;
use App\Admin\Traits\MinistryStateTrait;
use App\Entity\Contact;
use App\Entity\Mailing;
use App\Entity\MailingContact;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class MailingAdmin extends AbstractAppAdmin
{
    use CategoryTrait;
    use MinistryStateTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $now = new \DateTime();
        $formMapper
            ->with('app.mailing.groups.default')
                ->add('subject', TextType::class)
                ->add('greetingType', ChoiceFieldMaskType::class, [
                    'choices' => [
                        'app.mailing.entity.greeting_type_choices.none' => Mailing::GREETING_TYPE_NONE,
                        'app.mailing.entity.greeting_type_choices.prepend' => Mailing::GREETING_TYPE_PREPEND,
                    ],
                    'map' => [
                        Mailing::GREETING_TYPE_NONE => ['textPlain'],
                        Mailing::GREETING_TYPE_PREPEND => ['greeting', 'textPlain'],
                    ],
                    'required' => true,
                ])
                ->add('greeting', TextType::class, [
                    'label' => false,
                    'mapped' => false,
                    'disabled' => true,
                    'data' => 'Sehr geehrte(r) Frau/Herr Mustermann,',
                ])
                ->add('textPlain', TextareaType::class, [
                    'required' => true,
                ]);
        $formMapper->end();
        $formMapper->with('app.mailing.groups.options', ['class' => 'col-md-6']);
        $formMapper
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'app.mailing.entity.status_choices.new' => Mailing::STATUS_NEW,
                    'app.mailing.entity.status_choices.prepared' => Mailing::STATUS_PREPARED,
                    'app.mailing.entity.status_choices.active' => Mailing::STATUS_ACTIVE,
                    'app.mailing.entity.status_choices.finished' => Mailing::STATUS_FINISHED,
                    'app.mailing.entity.status_choices.cancelled' => Mailing::STATUS_CANCELLED,
                ],
                'required' => true,
            ])
            ->add('startAt', DateTimePickerType::class, [
                'years' => range((int)$now->format('Y'), (int)$now->format('Y') + 2),
                //'dp_min_date' => '1-1-' . $now->format('Y'),
                //'dp_max_date' => $now->format('c'),
                'dp_use_seconds' => false,
                'dp_use_current' => false,
                'dp_minute_stepping' => 5,
                'required' => false,
            ]);
        $formMapper->end();
        $formMapper->with('app.mailing.groups.recipients', ['class' => 'col-md-6']);
        $this->addStateMinistriesFormFields($formMapper);
        $formMapper->add('serviceProviders', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
        $formMapper->add('communes', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
        $this->addCategoriesFormFields($formMapper);
        $formMapper->add('excludeContacts', ModelAutocompleteType::class, [
            'property' => ['firstName', 'lastName', 'email', 'organisation'],
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            //'choice_translation_domain' => false,
        ]);
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('subject');
        $this->addCategoriesDatagridFilters($datagridMapper);
        $this->addStateMinistriesDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('createdAt')
            ->add('status', 'choice', [
                'editable' => true,
                'choices' => [
                    Mailing::STATUS_NEW => 'app.mailing.entity.status_choices.new',
                    Mailing::STATUS_PREPARED => 'app.mailing.entity.status_choices.prepared',
                    Mailing::STATUS_ACTIVE => 'app.mailing.entity.status_choices.active',
                    Mailing::STATUS_FINISHED => 'app.mailing.entity.status_choices.finished',
                    Mailing::STATUS_CANCELLED => 'app.mailing.entity.status_choices.cancelled',
                ],
                'catalogue' => 'messages',
            ])
            ->add('subject')
            ->add('recipientCount')
            ->add('sendStartAt')
            ->add('sendEndAt')
            ->add('sentCount');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('createdAt')
            ->add('status', 'choice', [
                'editable' => true,
                'choices' => [
                    Mailing::STATUS_NEW => 'app.mailing.entity.status_choices.new',
                    Mailing::STATUS_PREPARED => 'app.mailing.entity.status_choices.prepared',
                    Mailing::STATUS_ACTIVE => 'app.mailing.entity.status_choices.active',
                    Mailing::STATUS_FINISHED => 'app.mailing.entity.status_choices.finished',
                    Mailing::STATUS_CANCELLED => 'app.mailing.entity.status_choices.cancelled',
                ],
                'catalogue' => 'messages',
            ])
            ->add('subject')
            ->add('textPlain')
            ->add('startAt')
            ->add('sendStartAt')
            ->add('sendEndAt')
            ->add('sentCount')
            ->add('recipientCount')
            ->add('mailingContacts', null, [
                'template' => 'Mailing/show-mailing-contacts.html.twig',
            ])
            ->add('excludeContacts')
            ->add('serviceProviders')
            ->add('communes');
        $this->addStateMinistriesShowFields($showMapper);
        $this->addCategoriesShowFields($showMapper);
    }

    public function preUpdate($object)
    {
        /** @var Mailing $object */
        if (!in_array($object->getStatus(), [
            Mailing::STATUS_FINISHED,
            Mailing::STATUS_CANCELLED,
        ])) {
            $this->updateMailingContacts($object);
        }
        $object->updateSentCount();
    }

    public function prePersist($object)
    {
        /** @var Mailing $object */
        if (!in_array($object->getStatus(), [
            Mailing::STATUS_FINISHED,
            Mailing::STATUS_CANCELLED,
        ])) {
            $this->updateMailingContacts($object);
            //$this->updateSentCount();
        }
    }

    /**
     * Update the mailing contact list; add all contacts of selected categories, state ministries, service providers
     * and communes
     * @param Mailing $object
     */
    public function updateMailingContacts(Mailing $object)
    {
        $categories = $object->getCategories();
        foreach ($categories as $child) {
            $contacts = $child->getContacts();
            foreach ($contacts as $contact) {
                $this->addContactToMailingOnce($object, $contact);
            }
        }
        $stateMinistries = $object->getStateMinistries();
        foreach ($stateMinistries as $child) {
            $contacts = $child->getContacts();
            foreach ($contacts as $contact) {
                $this->addContactToMailingOnce($object, $contact);
            }
        }
        $serviceProviders = $object->getServiceProviders();
        foreach ($serviceProviders as $child) {
            $contacts = $child->getContacts();
            foreach ($contacts as $contact) {
                $this->addContactToMailingOnce($object, $contact);
            }
        }
        $communes = $object->getCommunes();
        foreach ($communes as $child) {
            $contacts = $child->getContacts();
            foreach ($contacts as $contact) {
                $this->addContactToMailingOnce($object, $contact);
            }
        }
        $mailingContacts = $object->getMailingContacts();
        foreach ($mailingContacts as $child) {
            if ($object->contactIsBlacklisted($child->getContact())) {
                $this->modelManager->delete($child);
                $object->removeMailingContact($child);
            }
        }
        $object->setRecipientCount(count($mailingContacts));
    }

    /**
     * Add the given contact to the mailing; check if contact already exists before adding
     * Contacts set in excludeContacts will be skipped
     *
     * @param Mailing $object
     * @param Contact $contact
     */
    private function addContactToMailingOnce(Mailing $object, Contact $contact) {

        if (!$object->contactIsBlacklisted($contact)) {
            $contactIds = $object->getContactIds();
            if (!in_array($contact->getId(), $contactIds)) {
                $mailingContact = new MailingContact();
                $mailingContact->setSendStatus(Mailing::STATUS_NEW);
                if (in_array($object->getStatus(), [Mailing::STATUS_PREPARED])) {
                    $mailingContact->setSendStatus(Mailing::STATUS_PREPARED);
                }
                $mailingContact->setMailing($object);
                $mailingContact->setContact($contact);
                $this->modelManager->create($mailingContact);
                $object->addMailingContact($mailingContact);
            }
        }
    }
}
