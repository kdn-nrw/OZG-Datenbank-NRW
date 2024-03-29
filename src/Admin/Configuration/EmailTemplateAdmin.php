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

namespace App\Admin\Configuration;

use App\Admin\AbstractAppAdmin;
use App\Service\Mailer\InjectEmailTemplateManagerTrait;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class EmailTemplateAdmin extends AbstractAppAdmin
{
    use InjectEmailTemplateManagerTrait;

    protected $baseRoutePattern = 'configuration/email-templates';

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('general', [
                'label' => 'app.email_template.groups.general',
                'class' => 'col-xs-12',
            ])
            ->add('templateKey', ChoiceType::class, [
                'choices' => array_flip($this->emailTemplateManager->getEmailTemplatesChoices()),
                'attr' => [
                    'class' => 'form-control',
                    'data-sonata-select2' => 'false'
                ],
                'label' => false,
                'required' => true,
                //'disabled' => true,
            ])
            ->add('hidden', CheckboxType::class, [
                'label' => 'app.email_template.entity.hidden',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                //'format' => 'richhtml',
                //'ckeditor_context' => 'default', // optional
            ])
            ->add('senderEmail', EmailType::class, [
                'required' => true
            ])
            ->add('senderName', TextType::class, [
                'required' => false
            ])
            ->add('defaultRecipient', EmailType::class, [
                'required' => true
            ])
            ->add('replyToEmail', EmailType::class, [
                'required' => false
            ])
            ->add('ccAddresses', TextareaType::class, [
                'required' => false,
            ])
            ->end();
        $form
            ->with('content', [
                'label' => 'app.email_template.groups.content',
                'class' => 'col-xs-12',
            ])
            ->add('subject', TextType::class, [
                'required' => true,
            ])
            ->add('body', TextareaType::class, [
                'required' => true,
            ])
            ->end();
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('templateKey', TemplateRegistryInterface::TYPE_CHOICE, [
                'editable' => false,
                'choices' => $this->emailTemplateManager->getEmailTemplatesChoices(),
                'catalogue' => 'messages'
            ])
            ->add('subject')
            ->add('defaultRecipient')
            ->add('hidden', null, [
                'label' => 'app.email_template.entity.hidden',
            ]);
        $list->add(ListMapper::NAME_ACTIONS, null, [
            'label' => 'app.common.actions',
            'translation_domain' => 'messages',
            'actions' => [
                'edit' => [],
            ]
        ]);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
        $collection->remove('export');
    }
}
