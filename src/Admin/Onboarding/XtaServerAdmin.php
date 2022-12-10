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
use App\Admin\Traits\DatePickerTrait;
use App\Entity\Onboarding\AbstractOnboardingEntity;
use App\Entity\Onboarding\XtaServer;
use App\Form\Type\CommuneType;
use App\Form\Type\OnboardingContactType;
use App\Form\Type\OnboardingDocumentType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Mapper\BaseGroupedMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Valid;

class XtaServerAdmin extends AbstractOnboardingAdmin implements AuditedEntityAdminInterface
{
    use AuditedEntityAdminTrait;
    use DatePickerTrait;

    protected $baseRoutePattern = 'onboarding/xta';

    protected function configureFormGroups(BaseGroupedMapper $form)
    {
        $form
            ->with('general', [
                'label' => 'app.xta_server.groups.general',
                'description' => 'app.xta_server.groups.general_description',
                'class' => 'col-md-12',
            ])
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $this->configureFormGroups($form);
        $enableRequiredFields = false;
        $form
            ->with('general');
        $form
            ->add('commune', CommuneType::class, [
                'label' => false,
                //'required' => true,
                'disabled' => true,
                'required' => false
            ], [
                'admin_code' => CommuneAdmin::class,
            ]);
        $form
            ->add('applicationType', ChoiceType::class, [
                'label' => 'app.xta_server.entity.application_type',
                'choices' => XtaServer::$applicationTypeChoices,
                'required' => $enableRequiredFields,
                'expanded' => true,
                'placeholder' => false,
                'choice_attr' => static function ($choice, $key, $value) {
                    return ['class' => 'onboarding-application-type js-toggle-info ob-application-type-' . $value, 'data-toggle' => 'ob-application-type', 'data-show' => $value,];
                },
                'help' => '<span class="text-danger ob-application-type ob-application-type-show-2" style="display: none">Der Zertifikatsaustausch kann auf dem XTA-Server erst dann erfolgen, wenn bereits vorher das Zertifikat im DVDV erfolgreich ausgetauscht wurde. Erst nach dem erfolgreichen Austausch des Zertifikats auf dem XTA-Server kann auch das Zertifikat im verwendeten Fachverfahren ausgetauscht werden.</span>',
            ]);
        $form
            ->add('state', TextType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('authorityCategory', TextType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('organizationalKey', TextType::class, [
                'required' => false,
                'disabled' => true,
            ]);
        /** @var XtaServer|null $subject */
        $subject = $this->getSubject();
        if ($subject instanceof XtaServer && null !== $communeType = $subject->getCommuneType()) {
            $form
                ->add('communeTypeName', TextType::class, [
                    'label' => 'app.xta_server.entity.commune_type',
                    'required' => false,
                    'disabled' => true,
                    'mapped' => false,
                    'data' => $communeType . ''
                ]);
        }
        $form
            ->add('intermediaryOperatorType', ChoiceType::class, [
                'label' => 'app.xta_server.entity.intermediary_operator_type',
                'choices' => XtaServer::$intermediaryOperatorTypeChoices,
                'required' => $enableRequiredFields,
                'expanded' => true,
                'placeholder' => false,
                'choice_attr' => static function ($choice, $key, $value) {
                    return ['class' => 'onboarding-intermediary-operator-type ob-intermediary-operator-type-' . $value];
                },
            ]);
        $form
            ->add('comment', TextareaType::class, [
                'required' => false,
            ]);
        $this->addDataCompletenessConfirmedField($form);
        /*$form
            ->add('status', ChoiceType::class, [
                'label' => 'app.commune_info.entity.status',
                'choices' => array_flip(AbstractOnboardingEntity::$statusChoices),
                'required' => $enableRequiredFields,
                'expanded' => true,
                'choice_attr' => static function ($choice, $key, $value) {
                    return ['class' => 'onboarding-status ob-status-' . $value];
                },
            ]);*/
        $form->end();
        $this->addContactFormFields($form);
        $this->addDocumentFormFields($form, $enableRequiredFields);

    }

    protected function addContactFormFields(FormMapper $form)
    {
        $form
            ->with('contact_data', [
                'label' => 'app.xta_server.groups.contact_data',
                'class' => 'col-xs-12 col-md-6',
            ]);
        $form
            ->add('contact', OnboardingContactType::class, [
                'label' => false,
                'required' => false,
                'parent_admin' => $this,
                'show_contact_type' => false,
                'enable_external_user' => false,
                'enable_mobile_number' => false,
                'enable_phone_number' => true,
            ]);
        $form
            ->end();
    }

    protected function addDocumentFormFields(FormMapper $form, bool $enableRequiredFields)
    {
        $form->with('group_documents', [
            'label' => 'app.xta_server.groups.documents',
            'class' => 'col-xs-12 col-md-6',
        ]);
        $form->add('documents', CollectionType::class, [
            'label' => false,
            'allow_add' => false,
            'allow_delete' => true,
            'by_reference' => false,
            'entry_type' => OnboardingDocumentType::class,
            'entry_options' => [
                'parent_admin' => $this,
            ],
        ]);
        $form
            ->add('osciPrivateKeyPassword', TextType::class, [
                'required' => $enableRequiredFields,
            ]);
        $form->end();
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('organizationalKey')
            ->addConstraint(new Regex([
                'pattern' => "/^bab\:[\d]{5,8}/",
                'message' => $this->trans('app.xta_server.entity.organizational_key_validation_error'),
            ]))
            ->end();
        $errorElement
            ->with('documents')
            ->addConstraint(new Valid())
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        parent::configureDatagridFilters($filter);
        $filter
            ->add('applicationType', ChoiceFilter::class, [
                'label' => 'app.xta_server.entity.application_type',
                'field_options' => [
                    'choices' => XtaServer::$applicationTypeChoices,
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'choice_translation_domain' => 'messages',
                ],
                'field_type' => ChoiceType::class,
            ])
            ->add('intermediaryOperatorType', ChoiceFilter::class, [
                'label' => 'app.xta_server.entity.intermediary_operator_type',
                'field_options' => [
                    'choices' => XtaServer::$intermediaryOperatorTypeChoices,
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'choice_translation_domain' => 'messages',
                ],
                'field_type' => ChoiceType::class,
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('commune', null, [
                'admin_code' => CommuneAdmin::class,
            ])
            ->add('applicationType', FieldDescriptionInterface::TYPE_CHOICE, [
                'label' => 'app.xta_server.entity.application_type',
                'choices' => array_flip(XtaServer::$applicationTypeChoices),
                'editable' => false,
                'catalogue' => 'messages',
            ])
            ->add('intermediaryOperatorType', FieldDescriptionInterface::TYPE_CHOICE, [
                'label' => 'app.xta_server.entity.intermediary_operator_type',
                'choices' => array_flip(XtaServer::$intermediaryOperatorTypeChoices),
                'editable' => false,
                'catalogue' => 'messages',
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
            ->add('commune', null, [
                'admin_code' => CommuneAdmin::class,
            ])
            ->add('modifiedAt')
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                'label' => 'app.commune_info.entity.status',
                'editable' => false,
                'choices' => AbstractOnboardingEntity::$statusChoices,
                'catalogue' => 'messages',
            ])
            ->add('customValues');
        $show
            ->add('applicationType', FieldDescriptionInterface::TYPE_CHOICE, [
                'label' => 'app.xta_server.entity.application_type',
                'choices' => array_flip(XtaServer::$applicationTypeChoices),
                'editable' => false,
                'catalogue' => 'messages',
            ])
            ->add('state')
            ->add('authorityCategory')
            ->add('organizationalKey')
            ->add('intermediaryOperatorType', FieldDescriptionInterface::TYPE_CHOICE, [
                'label' => 'app.xta_server.entity.intermediary_operator_type',
                'choices' => array_flip(XtaServer::$intermediaryOperatorTypeChoices),
                'editable' => false,
                'catalogue' => 'messages',
            ])
            ->add('contact')
            ->add('comment');
        $show->add('documents', null, [
            'template' => 'General/Show/show-attachments.html.twig',
        ]);
        $show
            ->add('osciPrivateKeyPassword');
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('download', $this->getRouterIdParameter() . '/download');
    }
}
