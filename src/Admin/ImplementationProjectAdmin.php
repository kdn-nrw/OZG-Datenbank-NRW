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
use App\Admin\Traits\LaboratoryTrait;
use App\Admin\Traits\OrganisationTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Admin\Traits\ServiceTrait;
use App\Admin\Traits\SolutionTrait;
use App\Entity\ImplementationProject;
use App\Entity\ImplementationStatus;
use App\Entity\Service;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class ImplementationProjectAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use ContactTrait;
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
        $now = new DateTime();
        $maxYear = (int)$now->format('Y') + 2;
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
            ])
            ->add('projectStartAt', DatePickerType::class, [
                //'years' => range(2018, (int)$now->format('Y') + 2),
                'dp_min_date' => new DateTime('2018-01-01 00:00:00'),
                'dp_max_date' => new DateTime($maxYear . '-12-31 23:59:59'),
                'dp_use_current' => false,
                'datepicker_use_button' => true,
                'required' => false,
            ])
            ->add('notes', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $this->addContactsFormFields($formMapper, false, false, 'contacts', false);
        $this->addOrganisationsFormFields($formMapper, 'interestedOrganisations');
        $this->addOrganisationsFormFields($formMapper, 'participationOrganisations');
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
        $this->addFullTextDatagridFilter($datagridMapper);
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
        $datagridMapper->add('projectStartAt');
        $this->addContactsDatagridFilters($datagridMapper);
        $this->addOrganisationsDatagridFilters($datagridMapper, 'interestedOrganisations');
        $this->addOrganisationsDatagridFilters($datagridMapper, 'participationOrganisations');
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
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
            ])
            ->add('projectStartAt', null, [
                // https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details
                'pattern' => 'MMMM yyyy',
            ]);
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
            ])
            ->add('projectStartAt', null, [
                // https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details
                'pattern' => 'MMMM yyyy',
            ])
            ->add('notes', 'html');
        $this->addLaboratoriesShowFields($showMapper);
        $this->addSolutionsShowFields($showMapper);
        $this->addContactsShowFields($showMapper);
        $this->addOrganisationsShowFields($showMapper, 'interestedOrganisations');
        $this->addOrganisationsShowFields($showMapper, 'participationOrganisations');
        $this->addServiceSystemsShowFields($showMapper);
        $this->addServicesShowFields($showMapper);
        $showMapper->add('bureaus', null, [
            'admin_code' => BureauAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-bureaus.html.twig',
        ]);
        $showMapper->add('portals', null, [
            'label' => 'app.implementation_project.entity.portals',
            'admin_code' => PortalAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-portals.html.twig',
        ]);
    }
}
