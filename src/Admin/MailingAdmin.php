<?php

namespace App\Admin;

use App\Admin\Traits\CategoryTrait;
use App\Admin\Traits\MinistryStateTrait;
use App\Entity\Mailing;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
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
            ->tab('app.mailing.tabs.default')
            ->with('general', [
                'label' => false,
            ])
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
            ->add('subject', TextType::class)
            ->add('textPlain', TextareaType::class, [
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
            ->add('mailingContacts')
            ->add('excludeContacts')
            ->add('serviceProviders')
            ->add('communes');
        $this->addStateMinistriesShowFields($showMapper);
        $this->addCategoriesShowFields($showMapper);
    }
}
