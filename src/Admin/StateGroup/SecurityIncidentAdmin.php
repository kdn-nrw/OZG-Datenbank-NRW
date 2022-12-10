<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\StateGroup;

use App\Admin\AbstractAppAdmin;
use App\Admin\Traits\DatePickerTrait;
use App\Entity\StateGroup\SecurityIncident;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SecurityIncidentAdmin extends AbstractAppAdmin
{
    use DatePickerTrait;

    protected function configureFormFields(FormMapper $form): void
    {
        if (!$this->isExcludedFormField('serviceProvider')) {
            $form
                ->add('serviceProvider', ModelType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => ServiceProviderAdmin::class
                ]);
        }
        $this->addDatePickerFormField($form, 'occurredOn');
        $this->addDatePickerFormField($form, 'solvedOn');

        $form
            ->add('subjectType', ChoiceType::class, [
                'label' => 'app.security_incident.entity.subject_type',
                'choices' => array_flip(SecurityIncident::$subjectTypeChoices),
                'required' => true,
                'expanded' => false,
                'choice_attr' => static function($choice, $key, $value) {
                    return ['class' => 'security-type security-type-' . $value];
                },
                'help' => 'app.security_incident.entity.subject_type_help',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'app.security_incident.entity.description',
                'required' => true,
                //'format' => 'richhtml',
                //'ckeditor_context' => 'default', // optional
                'help' => 'app.security_incident.entity.description_help',
            ])
            ->add('affected', TextType::class, [
                'required' => true,
                'help' => 'app.security_incident.entity.affected_help',
            ])
            ->add('extent', ChoiceType::class, [
                'label' => 'app.security_incident.entity.extent',
                'choices' => array_flip(SecurityIncident::$extentChoices),
                'required' => true,
                'expanded' => false,
                'choice_attr' => static function($choice, $key, $value) {
                    return ['class' => 'security-extent security-extent-' . $value];
                },
                'help' => 'app.security_incident.entity.extent_help',
            ])
            ->add('method', ChoiceType::class, [
                'label' => 'app.security_incident.entity.method',
                'choices' => array_flip(SecurityIncident::$methodChoices),
                'required' => true,
                'expanded' => false,
                'choice_attr' => static function($choice, $key, $value) {
                    return ['class' => 'security-method security-method-' . $value];
                },
                'help' => 'app.security_incident.entity.method_help',
            ])
            ->add('cause', TextareaType::class, [
                'label' => 'app.security_incident.entity.cause',
                'required' => false,
                'help' => 'app.security_incident.entity.cause_help',
            ])
            ->add('measures', TextareaType::class, [
                'label' => 'app.security_incident.entity.measures',
                'required' => false,
                'help' => 'app.security_incident.entity.measures_help',
            ])
            ->add('informedParties', TextareaType::class, [
                'label' => 'app.security_incident.entity.informed_parties',
                'required' => false,
                'help' => 'app.security_incident.entity.informed_parties_help',
            ]);

        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $this->addDefaultDatagridFilter($filter, 'serviceProvider');
        $filter->add('subjectType',
            null, [
                'field_type' => ChoiceType::class,
            ],
            [
                'choices' => array_flip(SecurityIncident::$subjectTypeChoices)
            ]
        );
        $filter->add('extent',
            null, [
                'field_type' => ChoiceType::class,
            ],
            [
                'choices' => array_flip(SecurityIncident::$extentChoices)
            ]
        );
        $filter->add('method',
            null, [
                'field_type' => ChoiceType::class,
            ],
            [
                'choices' => array_flip(SecurityIncident::$methodChoices)
            ]
        );
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('serviceProvider', null, [
                'admin_code' => ServiceProviderAdmin::class
            ])
            ->add('createdBy', null, [
                'template' => 'General/List/list_user.html.twig',
            ]);
        $this->addDatePickersListFields($list, 'occurredOn', false, false);
        $this->addDatePickersListFields($list, 'solvedOn', false, false);
        $list
            ->add('subjectType', 'choice', [
                'editable' => false,
                'choices' => SecurityIncident::$subjectTypeChoices,
                'catalogue' => 'messages',
            ])
            ->add('extent', 'choice', [
                'editable' => false,
                'choices' => SecurityIncident::$extentChoices,
                'catalogue' => 'messages',
            ])
            ->add('method', 'choice', [
                'editable' => false,
                'choices' => SecurityIncident::$methodChoices,
                'catalogue' => 'messages',
            ]);
        $list->add(ListMapper::NAME_ACTIONS, null, [
            'label' => 'app.common.actions',
            'translation_domain' => 'messages',
            'actions' => [
                'show' => [],
                'edit' => [],
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('serviceProvider')
            ->add('createdBy', null, [
                'template' => 'General/Show/show-user.html.twig',
            ]);
        $this->addDatePickersShowFields($show, 'createdAt', false);
        $this->addDatePickersShowFields($show, 'occurredOn', false);
        $this->addDatePickersShowFields($show, 'solvedOn', false);
        $show->add('subjectType', 'choice', [
                'editable' => false,
                'choices' => SecurityIncident::$subjectTypeChoices,
                'catalogue' => 'messages',
            ])
            ->add('description')
            ->add('affected')
            ->add('extent', 'choice', [
                'editable' => false,
                'choices' => SecurityIncident::$extentChoices,
                'catalogue' => 'messages',
            ])
            ->add('method', 'choice', [
                'editable' => false,
                'choices' => SecurityIncident::$methodChoices,
                'catalogue' => 'messages',
            ])
            ->add('cause')
            ->add('measures')
            ->add('informedParties');
    }
}
