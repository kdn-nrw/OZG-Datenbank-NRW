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
use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\OrganisationOneToOneTrait;
use App\Entity\Contact;
use App\Entity\Organisation;
use App\Entity\OrganisationEntityInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class CentralAssociationAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected $baseRoutePattern = 'state/centralassociation';

    use CommuneTrait;
    use ContactTrait;
    use AddressTrait;
    use OrganisationOneToOneTrait;

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.central_association.entity.organisation_contacts' => 'app.organisation.entity.contacts',
        'app.central_association.entity.organisation_url' => 'app.organisation.entity.url',
        'app.central_association.entity.organisation_street' => 'app.organisation.entity.street',
        'app.central_association.entity.organisation_zip_code' => 'app.organisation.entity.zip_code',
        'app.central_association.entity.organisation_town' => 'app.organisation.entity.town',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->tab('default', ['label' => 'app.central_association.tabs.default']);
        $form->with('general', [
            'label' => 'app.central_association.tabs.default',
        ]);
        $this->addOrganisationOneToOneFormFields($form);
        $form->add('shortName', TextType::class);
        $this->addCommunesFormFields($form);
        $form->end();
        $form->end();
    }

    protected function preUpdate(object $object): void
    {
        /** @var OrganisationEntityInterface $object */
        $this->updateOrganisation($object);
    }

    protected function prePersist(object $object): void
    {
        /** @var OrganisationEntityInterface $object */
        $this->updateOrganisation($object);
    }

    private function updateOrganisation(OrganisationEntityInterface $object)
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->getModelManager();
        $organisation = $object->getOrganisation();
        $organisation->setFromReference($object);
        $orgEm = $modelManager->getEntityManager(Organisation::class);
        if (!$orgEm->contains($organisation)) {
            $orgEm->persist($organisation);
        }
        $contacts = $organisation->getContacts();
        $contactEm = $modelManager->getEntityManager(Contact::class);
        foreach ($contacts as $contact) {
            if (!$contactEm->contains($contact)) {
                $contact->setOrganisation($organisation);
                $contactEm->persist($contact);
            }
        }
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name')
            ->add('shortName');
        $this->addOrganisationOneToOneDatagridFilters($filter);
        $this->addDefaultDatagridFilter($filter, 'communes');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name')
            ->add('shortName');
        $this->addOrganisationOneToOneListFields($list);
        //$this->addCommunesListFields($list);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name')
            ->add('shortName');
        $this->addOrganisationOneToOneShowFields($show);
        $this->addCommunesShowFields($show);
    }
}
