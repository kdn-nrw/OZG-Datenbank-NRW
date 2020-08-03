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

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\OrganisationOneToOneTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
use App\Entity\Contact;
use App\Entity\Organisation;
use App\Entity\OrganisationEntityInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ServiceProviderAdmin extends AbstractAppAdmin
{
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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('default', ['label' => 'app.service_provider.group.general_data']);
        $formMapper->with('general', [
            'label' => 'app.service_provider.group.general_data',
        ]);
        $this->addOrganisationOneToOneFormFields($formMapper);
        $formMapper->add('shortName', TextType::class);
        $formMapper->end();
        $formMapper->with('services_data', [
            'label' => 'app.service_provider.group.reference_data',
        ]);
        $this->addCommunesFormFields($formMapper);
        $this->addSpecializedProceduresFormFields($formMapper);
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
        $this->addFullTextDatagridFilter($datagridMapper);
        $datagridMapper->add('name')
            ->add('shortName');
        $this->addOrganisationOneToOneDatagridFilters($datagridMapper);
        $this->addCommunesDatagridFilters($datagridMapper);
        $this->addSpecializedProceduresDatagridFilters($datagridMapper);
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
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        $exportFields = parent::getExportFields();
        $exportFields[] = 'manufacturers';
        return $exportFields;
    }

    /**
     * @return array
     */
    protected function getExportExcludeFields(): array
    {
        return ['hidden', 'specializedProcedures.manufacturers'];
    }
}
