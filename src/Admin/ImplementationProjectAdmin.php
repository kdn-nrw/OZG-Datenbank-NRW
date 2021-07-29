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
use Sonata\Form\Type\CollectionType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class ImplementationProjectAdmin extends AbstractAppAdmin implements ExtendedSearchAdminInterface, EnableFullTextSearchAdminInterface
{
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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('app.implementation_project.tabs.general', ['tab' => true])
            ->with('general', [
                'label' => 'app.implementation_project.groups.general_data',
                'class' => 'col-md-6'
            ]);
        $formMapper->add('name', TextType::class);
        $this->addSolutionsFormFields($formMapper);
        $formMapper
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addLaboratoriesFormFields($formMapper);
        $formMapper->end();
        $formMapper
            ->with('dates', [
                'label' => 'app.implementation_project.groups.dates',
                'class' => 'col-md-6'
            ]);
        $this->addDatePickerFormField($formMapper, 'projectStartAt');
        $this->addDatePickerFormField($formMapper, 'conceptStatusAt');
        $this->addDatePickerFormField($formMapper, 'implementationStatusAt');
        $this->addDatePickerFormField($formMapper, 'pilotingStatusAt');
        $this->addDatePickerFormField($formMapper, 'commissioningStatusAt');
        $this->addDatePickerFormField($formMapper, 'nationwideRolloutAt');
        $formMapper
            ->add('status', ModelType::class, [
                'label' => 'app.implementation_project.entity.status_form',
                'btn_add' => false,
                'required' => true,
                'expanded' => true,
                'query' => $this->getStatusQueryBuilder(),
                'choice_translation_domain' => false,
            ]);
        $formMapper->end();
        $formMapper
            ->with('references', [
                'label' => false,
            ]);
        $formMapper
            ->add('notes', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $this->addContactsFormFields($formMapper, false, false, 'contacts', false);
        $this->addOrganisationsFormFields($formMapper, 'projectLeaders');
        $this->addOrganisationsFormFields($formMapper, 'participationOrganisations');
        $this->addOrganisationsFormFields($formMapper, 'interestedOrganisations');
        $this->addFundingsFormFields($formMapper);
        $this->addContactsFormFields($formMapper, false, false, 'fimExperts', false);
        $this->addSlugFormField($formMapper, $this->getSubject());
        $formMapper->end()
            ->end()
            ->tab('app.implementation_project.tabs.services')
            ->with('service_solutions', [
                'label' => false,
            ]);
        $this->addServiceFormFields($formMapper);
        $formMapper->end();
        $formMapper->end();
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

    private function addServiceFormFields(FormMapper $formMapper): void
    {
        $formMapper
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
        $formMapper
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
        $formMapper->getFormBuilder()->addEventListener(FormEvents::POST_SET_DATA,
            static function (FormEvent $event) use ($formMapper, $subject, $em) {
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
                $formMapper->get('services')->setAttribute('query', $queryBuilder);
            });
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addDefaultDatagridFilter($datagridMapper, 'laboratories');
        $this->addDefaultDatagridFilter($datagridMapper, 'solutions');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSystems');
        $this->addDefaultDatagridFilter($datagridMapper, 'services.service');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSystems.situation.subject');
        $datagridMapper->add('status');
        $this->addDefaultDatagridFilter($datagridMapper, 'projectStartAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'conceptStatusAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'implementationStatusAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'pilotingStatusAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'commissioningStatusAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'nationwideRolloutAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'contacts');
        $this->addDefaultDatagridFilter($datagridMapper, 'projectLeaders');
        $this->addDefaultDatagridFilter($datagridMapper, 'participationOrganisations');
        $this->addDefaultDatagridFilter($datagridMapper, 'interestedOrganisations');
        $this->addDefaultDatagridFilter($datagridMapper, 'fundings');
        $this->addDefaultDatagridFilter($datagridMapper, 'services.service.bureaus');
        $this->addDefaultDatagridFilter($datagridMapper, 'services.service.portals', ['label' => 'app.implementation_project.entity.portals']);
        $this->addDefaultDatagridFilter($datagridMapper, 'services.service.communeTypes', ['label' => 'app.service_system.entity.commune_types']);
        $this->addDefaultDatagridFilter($datagridMapper, 'fimExperts');
        $this->addDefaultDatagridFilter($datagridMapper, 'solutions.openDataItems');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('status', 'choice', [
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
            ]);;
        $this->addDatePickersListFields($listMapper, 'projectStartAt', true);
        $this->addDatePickersListFields($listMapper, 'conceptStatusAt', true);
        $this->addDatePickersListFields($listMapper, 'implementationStatusAt', true);
        $this->addDatePickersListFields($listMapper, 'pilotingStatusAt', true);
        $this->addDatePickersListFields($listMapper, 'commissioningStatusAt', true);
        $this->addDatePickersListFields($listMapper, 'nationwideRolloutAt', true);
        $this->addServiceSystemsListFields($listMapper);
        //$this->addSolutionsListFields($listMapper);
        $this->addDefaultListActions($listMapper);
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
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('description')
            ->add('status', 'choice', [
                //'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
            ]);
        $showMapper
            ->add('notes', 'html', [
                'template' => 'ImplementationProjectAdmin/show-notes.html.twig',
                'is_custom_field' => true,
            ]);
        $showMapper->add('statusInfo', null, [
            'admin_code' => ImplementationStatusAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-status-info.html.twig',
            'is_custom_field' => true,
        ]);
        $this->addLaboratoriesShowFields($showMapper);
        $this->addSolutionsShowFields($showMapper);
        $this->addContactsShowFields($showMapper, false, 'contacts');
        $this->addOrganisationsShowFields($showMapper, 'projectLeaders');
        $this->addOrganisationsShowFields($showMapper, 'participationOrganisations');
        $this->addOrganisationsShowFields($showMapper, 'interestedOrganisations');
        $this->addServiceSystemsShowFields($showMapper);

        $showMapper->add('services', null, [
            'admin_code' => ServiceAdmin::class,
            'showFimTypes' => true,
            'template' => 'ImplementationProjectAdmin/Show/show-project-services.html.twig',
            'is_custom_field' => true,
            'showProject' => false,
        ]);
        $this->addFundingsShowFields($showMapper);
        $showMapper->add('bureaus', null, [
            'admin_code' => BureauAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-bureaus.html.twig',
        ]);
        $showMapper->add('portals', null, [
            'label' => 'app.implementation_project.entity.portals',
            'admin_code' => PortalAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-portals.html.twig',
        ]);
        $this->addContactsShowFields($showMapper, false, 'fimExperts');
    }
}
