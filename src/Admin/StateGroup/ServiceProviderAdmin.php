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

namespace App\Admin\StateGroup;

use App\Admin\AbstractAppAdmin;
use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\ManufacturerAdmin;
use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\OrganisationOneToOneTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
use App\Entity\Contact;
use App\Entity\Organisation;
use App\Entity\OrganisationEntityInterface;
use App\Model\ExportSettings;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceProviderAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected $baseRoutePattern = 'state/serviceprovider';

    use CommuneTrait;
    use ContactTrait;
    use AddressTrait;
    use OrganisationOneToOneTrait;
    use SpecializedProcedureTrait;

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.service_provider.entity.organisation_contacts' => 'app.organisation.entity.contacts',
        'app.service_provider.entity.organisation_url' => 'app.organisation.entity.url',
        'app.service_provider.entity.organisation_street' => 'app.organisation.entity.street',
        'app.service_provider.entity.organisation_zip_code' => 'app.organisation.entity.zip_code',
        'app.service_provider.entity.organisation_town' => 'app.organisation.entity.town',
    ];

    protected function configureTabMenu(ItemInterface $menu, $action, ?AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        if (null !== $admin) {
            $id = $admin->getRequest()->get('id');

            $menu->addChild('app.service_provider.actions.show', [
                'uri' => $admin->generateUrl('show', ['id' => $id])
            ]);

            if ($this->isGranted('EDIT')) {
                $menu->addChild('app.service_provider.actions.edit', [
                    'uri' => $admin->generateUrl('edit', ['id' => $id])
                ]);
            }

            if ($this->isGranted('LIST')) {
                $menu->addChild('app.security_incident.list', [
                    'uri' => $admin->getChild(SecurityIncidentAdmin::class)->generateUrl('list', ['id' => $id])
                ]);
            }
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('default', ['label' => 'app.service_provider.group.general_data']);
        $formMapper->with('general', [
            'label' => false,
        ]);
        $this->addOrganisationOneToOneFormFields($formMapper);
        $formMapper->add('shortName', TextType::class, [
            'required' => false,
        ]);
        $formMapper->end();
        $formMapper->end();

        $formMapper->tab('app.service_provider.group.reference_data');
        $formMapper->with('services_data', [
            'label' => false,
        ]);
        $this->addCommunesFormFields($formMapper);
        $this->addSpecializedProceduresFormFields($formMapper);
        $formMapper->end();
        $formMapper->end();

        $formMapper->tab('app.service_provider.group.security_incident');
        $formMapper->with('security_incident', [
            'label' => false,
        ]);
        $formMapper->add('securityIncidents', CollectionType::class, [
            'label' => false,
            'type_options' => [
                'delete' => true,
            ],
            'by_reference' => false,
        ], [
            'edit' => 'inline',
            'inline' => 'natural',
            'ba_custom_hide_fields' => ['serviceProvider'],
        ]);
        $formMapper->end();
        $formMapper->end();
    }

    public function preUpdate($object)
    {
        /** @var OrganisationEntityInterface $object */
        $this->updateOrganisation($object);
    }

    public function prePersist($object)
    {
        /** @var OrganisationEntityInterface $object */
        $this->updateOrganisation($object);
    }

    private function updateOrganisation(OrganisationEntityInterface $object)
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->getModelManager();
        /** @var OrganisationEntityInterface $object */
        $organisation = $object->getOrganisation();
        $organisation->setFromReference($object);
        $orgEm = $modelManager->getEntityManager(Organisation::class);
        if (!$orgEm->contains($organisation)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $orgEm->persist($organisation);
        }
        $contacts = $organisation->getContacts();
        $contactEm = $modelManager->getEntityManager(Contact::class);
        foreach ($contacts as $contact) {
            if (!$contactEm->contains($contact)) {
                $contact->setOrganisation($organisation);
                /** @noinspection PhpUnhandledExceptionInspection */
                $contactEm->persist($contact);
            }
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name')
            ->add('shortName');
        $this->addOrganisationOneToOneDatagridFilters($datagridMapper);
        $this->addDefaultDatagridFilter($datagridMapper, 'communes');
        $this->addDefaultDatagridFilter($datagridMapper, 'specializedProcedures');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('shortName')
            ->add('organisation.url', 'url');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('shortName');
        $this->addOrganisationOneToOneShowFields($showMapper);
        $this->addCommunesShowFields($showMapper);
        $this->addSpecializedProceduresShowFields($showMapper);
        $showMapper->add('specializedProcedures.manufacturers', null, [
            'label' => 'app.specialized_procedure.entity.manufacturers',
            'admin_code' => ManufacturerAdmin::class,
            'template' => 'General/show-specialized-procedures-manufacturers.html.twig',
        ]);
        $showMapper->add('securityIncidents');
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['specializedProcedures.manufacturers']);
        $settings->setAdditionFields(['manufacturers']);
        return $settings;
    }
}
