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

use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\DatePickerTrait;
use App\Admin\Traits\FundingTrait;
use App\Admin\Traits\LaboratoryTrait;
use App\Admin\Traits\OrganisationTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Admin\Traits\ServiceTrait;
use App\Admin\Traits\SolutionTrait;
use App\Entity\ImplementationProject;
use App\Entity\ImplementationStatus;
use App\Entity\Service;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
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
    use ServiceTrait;
    use ServiceSystemTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('app.implementation_project.tabs.general', ['tab' => true])
            ->with('general', [
                'label' => false,
            ]);
        $formMapper->add('name', TextType::class);
        $this->addSolutionsFormFields($formMapper);
        $formMapper
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addLaboratoriesFormFields($formMapper);
        $formMapper
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
                'choice_translation_domain' => false,
            ]);
        $this->addDatePickerFormField($formMapper, 'projectStartAt');
        $formMapper
            ->add('notes', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $this->addContactsFormFields($formMapper, false, false, 'contacts', false);
        $this->addOrganisationsFormFields($formMapper, 'interestedOrganisations');
        $this->addOrganisationsFormFields($formMapper, 'participationOrganisations');
        $this->addOrganisationsFormFields($formMapper, 'projectLeaders');
        $this->addFundingsFormFields($formMapper);
        $this->addContactsFormFields($formMapper, false, false, 'fimExperts', false);
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
            );
        $formMapper->getFormBuilder()->addEventListener(FormEvents::POST_SET_DATA,
            static function (FormEvent $event) use ($formMapper, $subject, $em) {
                $serviceSystems = $subject->getServiceSystems();
                if (count($serviceSystems) > 0) {
                    /** @var QueryBuilder $queryBuilder */
                    $queryBuilder = $em->createQueryBuilder('s')
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
        $this->addLaboratoriesDatagridFilters($datagridMapper);
        $this->addSolutionsDatagridFilters($datagridMapper);
        $this->addServiceSystemsDatagridFilters($datagridMapper);
        $this->addServicesDatagridFilters($datagridMapper);
        $datagridMapper->add('serviceSystems.situation.subject',
            null,
            ['label' => 'app.situation.entity.subject'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status');

        $this->addDatePickersDatagridFilters($datagridMapper, 'projectStartAt');
        $this->addContactsDatagridFilters($datagridMapper, 'contacts');
        $this->addOrganisationsDatagridFilters($datagridMapper, 'interestedOrganisations');
        $this->addOrganisationsDatagridFilters($datagridMapper, 'participationOrganisations');
        $this->addOrganisationsDatagridFilters($datagridMapper, 'projectLeaders');
        $this->addFundingsDatagridFilters($datagridMapper);
        $datagridMapper->add('services.bureaus',
            null,
            ['label' => 'app.implementation_project.entity.bureaus'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('services.portals',
            null,
            ['label' => 'app.implementation_project.entity.portals'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $this->addContactsDatagridFilters($datagridMapper, 'fimExperts');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'status'],
                ]
            ]);

        $this->addDatePickersListFields($listMapper, 'projectStartAt');
        $this->addServiceSystemsListFields($listMapper);
        //$this->addSolutionsListFields($listMapper);
        $this->addDefaultListActions($listMapper);
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
        $this->addDatePickersShowFields($showMapper, 'projectStartAt');
        $showMapper
            ->add('notes', 'html');
        $this->addLaboratoriesShowFields($showMapper);
        $this->addSolutionsShowFields($showMapper);
        $this->addContactsShowFields($showMapper, false, 'contacts');
        $this->addOrganisationsShowFields($showMapper, 'interestedOrganisations');
        $this->addOrganisationsShowFields($showMapper, 'participationOrganisations');
        $this->addOrganisationsShowFields($showMapper, 'projectLeaders');
        $this->addServiceSystemsShowFields($showMapper);
        $this->addServicesShowFields($showMapper, ['showFimTypes' => true]);
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
