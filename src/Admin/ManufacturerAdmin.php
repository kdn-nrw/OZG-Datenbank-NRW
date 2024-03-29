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


class ManufacturerAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use AddressTrait;
    use ContactTrait;
    use OrganisationOneToOneTrait;
    use SpecializedProcedureTrait;

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.manufacturer.entity.organisation_contacts' => 'app.organisation.entity.contacts',
        'app.manufacturer.entity.organisation_url' => 'app.organisation.entity.url',
        'app.manufacturer.entity.organisation_street' => 'app.organisation.entity.street',
        'app.manufacturer.entity.organisation_zip_code' => 'app.organisation.entity.zip_code',
        'app.manufacturer.entity.organisation_town' => 'app.organisation.entity.town',
    ];

    protected function configureFormFields(FormMapper $form)
    {
        $this->addOrganisationOneToOneFormFields($form, ['organizationType', 'contacts']);

        $this->addSpecializedProceduresFormFields($form);
        $this->addContactsFormFields($form, true, 'organisation.contacts', false, false);
        $form
            ->end();
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

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'organisation.contacts', [
            'label' => 'app.manufacturer.entity.organisation__contacts',
        ]);
        $filter->add('organisation.zipCode');
        $filter->add('organisation.town');
        $this->addDefaultDatagridFilter($filter, 'specializedProcedures');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name');
        $this->addSpecializedProceduresListFields($list);
        $list
            ->add('organisation.url', 'url');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show->add('name');
        $show
            ->add('organisation.street')
            ->add('organisation.zipCode')
            ->add('organisation.town')
            ->add('organisation.url', 'url');
        $this->addSpecializedProceduresShowFields($show);
        $show->add('organisation.contacts', null, [
                'label' => 'app.manufacturer.entity.organisation__contacts',
                'admin_code' => ContactAdmin::class,
            ]);
    }
}
