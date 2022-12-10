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

use App\Entity\Mailing;
use App\Entity\MailingContact;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class MailingContactAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.mailing_contact.entity.mailing' => 'app.mailing.list',
        'app.service.entity.service_system_situation_subject' => 'app.situation.entity.subject',
        'app.service.entity.service_system_service_key' => 'app.service_system.entity.service_key',
        'app.service.entity.service_system_priority' => 'app.service_system.entity.priority',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('mailing', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('contact', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('sendStatus', ChoiceType::class, [
                'choices' => [
                    'app.mailing.entity.status_choices.new' => Mailing::STATUS_NEW,
                    'app.mailing.entity.status_choices.prepared' => Mailing::STATUS_PREPARED,
                    'app.mailing.entity.status_choices.active' => Mailing::STATUS_ACTIVE,
                    'app.mailing.entity.status_choices.finished' => Mailing::STATUS_FINISHED,
                    'app.mailing.entity.status_choices.cancelled' => Mailing::STATUS_CANCELLED,
                    'app.mailing_contact.entity.status_choices.failed' => MailingContact::SEND_STATUS_FAILED,
                    'app.mailing_contact.entity.status_choices.disabled' => MailingContact::SEND_STATUS_DISABLED,
                ],
                'required' => true,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('mailing');
        $filter->add('contact');
        $filter->add('sendStatus');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('contact')
            ->add('sentAt')
            ->add('sendAttempts')
            //->add('mailing')
            ->add('sendStatus', 'choice', [
                'editable' => true,
                'choices' => [
                    Mailing::STATUS_NEW => 'app.mailing.entity.status_choices.new',
                    Mailing::STATUS_PREPARED => 'app.mailing.entity.status_choices.prepared',
                    Mailing::STATUS_ACTIVE => 'app.mailing.entity.status_choices.active',
                    Mailing::STATUS_FINISHED => 'app.mailing.entity.status_choices.finished',
                    Mailing::STATUS_CANCELLED => 'app.mailing.entity.status_choices.cancelled',
                    MailingContact::SEND_STATUS_FAILED => 'app.mailing_contact.entity.status_choices.failed',
                    MailingContact::SEND_STATUS_DISABLED => 'app.mailing_contact.entity.status_choices.disabled',
                ],
                'catalogue' => 'messages',
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('contact')
            ->add('sentAt')
            ->add('sendAttempts')
            //->add('mailing')
            ->add('sendStatus', 'choice', [
                'editable' => true,
                'choices' => [
                    Mailing::STATUS_NEW => 'app.mailing.entity.status_choices.new',
                    Mailing::STATUS_PREPARED => 'app.mailing.entity.status_choices.prepared',
                    Mailing::STATUS_ACTIVE => 'app.mailing.entity.status_choices.active',
                    Mailing::STATUS_FINISHED => 'app.mailing.entity.status_choices.finished',
                    Mailing::STATUS_CANCELLED => 'app.mailing.entity.status_choices.cancelled',
                    MailingContact::SEND_STATUS_FAILED => 'app.mailing_contact.entity.status_choices.failed',
                    MailingContact::SEND_STATUS_DISABLED => 'app.mailing_contact.entity.status_choices.disabled',
                ],
                'catalogue' => 'messages',
            ]);
    }
}
