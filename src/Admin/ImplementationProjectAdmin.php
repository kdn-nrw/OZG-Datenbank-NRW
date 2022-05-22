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

use App\Admin\Base\AuditedEntityAdminInterface;
use App\Admin\Base\AuditedEntityAdminTrait;
use App\Admin\StateGroup\BureauAdmin;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\DatePickerTrait;
use App\Admin\Traits\FundingTrait;
use App\Admin\Traits\LaboratoryTrait;
use App\Admin\Traits\OrganisationTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Admin\Traits\SluggableTrait;
use App\Admin\Traits\SolutionTrait;
use App\Entity\ImplementationProject;
use App\Entity\ImplementationStatus;
use App\Entity\Service;
use App\Exporter\Source\ServiceListValueFormatter;
use App\Model\ExportSettings;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class ImplementationProjectAdmin extends AbstractAppAdmin implements ExtendedSearchAdminInterface, EnableFullTextSearchAdminInterface, AuditedEntityAdminInterface
{
    use AuditedEntityAdminTrait;
    use ContactTrait;
    use DatePickerTrait;
    use FundingTrait;
    use LaboratoryTrait;
    use OrganisationTrait;
    use SolutionTrait;
    use ServiceSystemTrait;
    use SluggableTrait;
/*
    protected function configureTabMenu(ItemInterface $menu, $action, ?AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && $action !== 'edit') {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        if (null !== $admin) {
            $id = $admin->getRequest()->get('id');

            $menu->addChild('app.implementation_project.actions.show', [
                'uri' => $admin->generateUrl('show', ['id' => $id])
            ]);

            if ($this->isGranted('EDIT')) {
                $menu->addChild('app.implementation_project.actions.edit', [
                    'uri' => $admin->generateUrl('edit', ['id' => $id])
                ]);
            }

            if ($this->isGranted('LIST')) {
                $menu->addChild('app.implementation_project.actions.list', [
                    'uri' => $admin->getChild(ImplementationProjectServiceAdmin::class)->generateUrl('list')
                ]);
            }
        }
    }*/

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('app.implementation_project.tabs.general', ['tab' => true])
            ->with('general', [
                'label' => 'app.implementation_project.groups.general_data',
                'class' => 'col-md-6'
            ]);
        $form->add('name', TextType::class);
        $this->addSolutionsFormFields($form);
        $form
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addLaboratoriesFormFields($form);
        $form
            ->add('efaType', ChoiceType::class, [
                'choices' => array_flip(ImplementationProject::EFA_TYPES),
                'attr' => [
                    'class' => 'form-control',
                    'data-sonata-select2' => 'false'
                ],
                'required' => false,
                'disabled' => false,
            ]);
        $form->end();
        $form
            ->with('dates', [
                'label' => 'app.implementation_project.groups.dates',
                'class' => 'col-md-6'
            ]);
        $this->addDatePickerFormField($form, 'projectStartAt', 5);
        $this->addDatePickerFormField($form, 'conceptStatusAt', 5);
        $this->addDatePickerFormField($form, 'implementationStatusAt', 5);
        $this->addDatePickerFormField($form, 'pilotingStatusAt', 5);
        $this->addDatePickerFormField($form, 'commissioningStatusAt', 5);
        $this->addDatePickerFormField($form, 'nationwideRolloutAt', 5);
        /*$form
            ->add('status', ModelType::class, [
                'label' => 'app.implementation_project.entity.status_form',
                'btn_add' => false,
                'required' => true,
                'expanded' => true,
                'query' => $this->getStatusQueryBuilder(),
                'choice_translation_domain' => false,
            ]);*/
        $form->end();
        $form
            ->with('references', [
                'label' => false,
            ]);
        $form
            ->add('notes', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $this->addContactsFormFields($form, false, false, 'contacts', false);
        $this->addOrganisationsFormFields($form, 'projectLeaders');
        $this->addOrganisationsFormFields($form, 'participationOrganisations');
        $this->addOrganisationsFormFields($form, 'interestedOrganisations');
        $this->addFundingsFormFields($form);
        $this->addContactsFormFields($form, false, false, 'fimExperts', false);
        $this->addSlugFormField($form, $this->getSubject());
        $form->end()
            ->end()
            ->tab('app.implementation_project.tabs.services')
            ->with('service_solutions', [
                'label' => false,
            ]);
        $this->addServiceFormFields($form);
        $form->end();
        $form->end();
    }

    /**
     * Returns the query builder for the status
     *
     * @return QueryBuilder
     */
    private function getStatusQueryBuilder(): QueryBuilder
    {
        /** @var EntityManager $em */
        $em = $this->modelManager->getEntityManager(ImplementationStatus::class);

        /** @var ImplementationProject $subject */
        $subject = $this->getSubject();
        $status = $subject->getStatus();
        $queryBuilder = $em->createQueryBuilder()
            ->select('s')
            ->from(ImplementationStatus::class, 's');
        if (null !== $status) {
            $queryBuilder->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('s.setAutomatically', 0),
                $queryBuilder->expr()->eq('s', $status->getId())
            ));
        } else {
            $queryBuilder->where('s.setAutomatically = 0');
        }
        $queryBuilder->orderBy('s.level', 'ASC');
        $queryBuilder->addOrderBy('s.name', 'ASC');
        return $queryBuilder;
    }

    private function addServiceFormFields(FormMapper $form): void
    {
        $form
            ->add('serviceSystems', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
                'attr' => [
                    'data-sonata-select2' => 'false',
                    'class' => 'js-advanced-select ba-field-servicesystem',
                    'data-reload-selector' => 'select.ba-field-services',
                ]
            ],
                [
                    'admin_code' => ServiceSystemAdmin::class,
                ]
            );

        $em = $this->modelManager->getEntityManager(Service::class);

        /** @var ImplementationProject $subject */
        $subject = $this->getSubject();
        $form
            ->add('services', CollectionType::class, [
                'label' => false,
                'type_options' => [
                    'delete' => true,
                ],
                'by_reference' => false,
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
                'ba_custom_exclude_fields' => ['implementationProject'],
            ])/*
            ->add('services', ModelType::class, [
                'property' => 'name',
                'placeholder' => '',
                //'query' => $queryBuilder,
                'required' => false,
                'multiple' => true,
                'choice_translation_domain' => false,
                //'group_by' => 'serviceSystem',
                'attr' => [
                    'data-sonata-select2' => 'false',
                    'class' => 'js-advanced-select ba-field-services',
                    'data-url' => $this->routeGenerator->generate('app_service_choices'),
                    'data-entity-id' => $subject->getId()
                ]
            ],
                [
                    'admin_code' => ServiceAdmin::class,
                ]
            )*/;
        $form->getFormBuilder()->addEventListener(FormEvents::POST_SET_DATA,
            static function (FormEvent $event) use ($form, $subject, $em) {
                $serviceSystems = $subject->getServiceSystems();
                if (count($serviceSystems) > 0) {
                    /** @var EntityManager $em */
                    /** @var QueryBuilder $queryBuilder */
                    $queryBuilder = $em->createQueryBuilder()
                        ->select('s')
                        ->from(Service::class, 's')
                        ->where('s.serviceSystem IN(:serviceSystems)')
                        ->setParameter('serviceSystems', $serviceSystems)
                        ->orderBy('s.name', 'ASC');
                } else {
                    $queryBuilder = null;
                }
                $form->get('services')->setAttribute('query', $queryBuilder);
            });
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'laboratories');
        $this->addDefaultDatagridFilter($filter, 'solutions');
        $this->addDefaultDatagridFilter($filter, 'serviceSystems');
        $this->addDefaultDatagridFilter($filter, 'services.service');
        $this->addDefaultDatagridFilter($filter, 'serviceSystems.situation.subject');
        $filter->add('status');
        $this->addDefaultDatagridFilter($filter, 'projectStartAt');
        $this->addDefaultDatagridFilter($filter, 'conceptStatusAt');
        $this->addDefaultDatagridFilter($filter, 'implementationStatusAt');
        $this->addDefaultDatagridFilter($filter, 'pilotingStatusAt');
        $this->addDefaultDatagridFilter($filter, 'commissioningStatusAt');
        $this->addDefaultDatagridFilter($filter, 'nationwideRolloutAt');
        $this->addDefaultDatagridFilter($filter, 'contacts');
        $this->addDefaultDatagridFilter($filter, 'projectLeaders');
        $this->addDefaultDatagridFilter($filter, 'participationOrganisations');
        $this->addDefaultDatagridFilter($filter, 'interestedOrganisations');
        $this->addDefaultDatagridFilter($filter, 'fundings');
        $this->addDefaultDatagridFilter($filter, 'services.service.bureaus');
        $this->addDefaultDatagridFilter($filter, 'services.service.portals', ['label' => 'app.implementation_project.entity.portals']);
        $this->addDefaultDatagridFilter($filter, 'services.service.communeTypes', ['label' => 'app.service_system.entity.commune_types']);
        $this->addDefaultDatagridFilter($filter, 'fimExperts');
        $this->addDefaultDatagridFilter($filter, 'solutions.openDataItems');
        $filter
            ->add('efaType', ChoiceFilter::class, [
                'label' => 'app.implementation_project.entity.efa_type',
                'field_options' => [
                    'choices' => array_flip(ImplementationProject::EFA_TYPES),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'choice_translation_domain' => 'messages',
                ],
                'field_type' => ChoiceType::class,
            ]);
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('serviceSystems.situation.subject', 'string', [
                'label' => 'app.situation.entity.subject',
                //'associated_property' => 'name',
                'template' => 'ImplementationProjectAdmin/list-service-system-subjects.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystems'],
                    ['fieldName' => 'situation'],
                    ['fieldName' => 'subject'],
                ],
                'enable_filter_add' => true,
            ])
            ->add('efaType', TemplateRegistryInterface::TYPE_CHOICE, [
                'label' => 'app.implementation_project.entity.efa_type',
                'editable' => false,
                'choices' => ImplementationProject::EFA_TYPES,
                'catalogue' => 'messages',
            ])
            ->add('status', TemplateRegistryInterface::TYPE_CHOICE, [
                'editable' => false,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
                'template' => 'ImplementationProjectAdmin/list-status.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'status'],
                ]
            ]);
        $this->addDatePickersListFields($list, 'projectStartAt', true);
        $this->addDatePickersListFields($list, 'conceptStatusAt', true);
        $this->addDatePickersListFields($list, 'implementationStatusAt', true);
        $this->addDatePickersListFields($list, 'pilotingStatusAt', true);
        $this->addDatePickersListFields($list, 'commissioningStatusAt', true);
        $this->addDatePickersListFields($list, 'nationwideRolloutAt', true);
        $this->addServiceSystemsListFields($list);
        //$this->addSolutionsListFields($list);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['statusInfo']);
        $customServiceFormatter = new ServiceListValueFormatter();
        $customServiceFormatter->setDisplayType(ServiceListValueFormatter::DISPLAY_SERVICE_KEY);
        $settings->addCustomPropertyValueFormatter('serviceKeys', $customServiceFormatter);
        $customServiceSystemFormatter = new ServiceListValueFormatter();
        $customServiceSystemFormatter->setDisplayType(ServiceListValueFormatter::DISPLAY_SERVICE_KEY);
        $settings->addCustomPropertyValueFormatter('serviceSystemKeys', $customServiceSystemFormatter);
        $settings->setAdditionFields([
            'status', 'projectStartAt', 'conceptStatusAt',
            'implementationStatusAt', 'pilotingStatusAt', 'commissioningStatusAt', 'nationwideRolloutAt',
            'serviceKeys', 'serviceSystemKeys',
        ]);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show->add('name')
            ->add('description')
            ->add('status', TemplateRegistryInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
            ]);
        $show
            ->add('notes', 'html', [
                'template' => 'ImplementationProjectAdmin/show-notes.html.twig',
                'is_custom_field' => true,
            ]);
        $show->add('statusInfo', null, [
            'admin_code' => ImplementationStatusAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-status-info.html.twig',
            'is_custom_field' => true,
        ]);
        $this->addLaboratoriesShowFields($show);
        $this->addSolutionsShowFields($show);
        $this->addContactsShowFields($show, false, 'contacts');
        $this->addOrganisationsShowFields($show, 'projectLeaders');
        $this->addOrganisationsShowFields($show, 'participationOrganisations');
        $this->addOrganisationsShowFields($show, 'interestedOrganisations');
        $this->addServiceSystemsShowFields($show);

        $show->add('services', null, [
            'admin_code' => ServiceAdmin::class,
            'showFimTypes' => true,
            'template' => 'ImplementationProjectAdmin/Show/show-project-services.html.twig',
            'is_custom_field' => true,
            'showProject' => false,
        ]);
        $this->addFundingsShowFields($show);
        $show->add('bureaus', null, [
            'admin_code' => BureauAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-bureaus.html.twig',
        ]);
        $show->add('portals', null, [
            'label' => 'app.implementation_project.entity.portals',
            'admin_code' => PortalAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-portals.html.twig',
        ]);
        $this->addContactsShowFields($show, false, 'fimExperts');
    }
}
