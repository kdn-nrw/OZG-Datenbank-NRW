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
use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\OrganisationOneToOneTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Entity\Contact;
use App\Entity\Organisation;
use App\Entity\OrganisationEntityInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class MinistryStateAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected $baseRoutePattern = 'state/ministrystate';

    use ContactTrait;
    use AddressTrait;
    use OrganisationOneToOneTrait;
    use ServiceSystemTrait;

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.ministry_state.entity.organisation_contacts' => 'app.organisation.entity.contacts',
        'app.ministry_state.entity.organisation_url' => 'app.organisation.entity.url',
        'app.ministry_state.entity.organisation_street' => 'app.organisation.entity.street',
        'app.ministry_state.entity.organisation_zip_code' => 'app.organisation.entity.zip_code',
        'app.ministry_state.entity.organisation_town' => 'app.organisation.entity.town',
    ];

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->tab('default', ['label' => 'app.ministry_state.tabs.default']);
        $form->with('general', [
            'label' => 'app.ministry_state.tabs.default',
        ]);
        $this->addOrganisationOneToOneFormFields($form);
        $form->add('shortName', TextType::class);
        $this->addServiceSystemsFormFields($form);
        $form->end();
        $form->end();
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
        $this->addOrganisationOneToOneDatagridFilters($filter);
        $this->addDefaultDatagridFilter($filter, 'serviceSystems');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name');
        $this->addOrganisationOneToOneListFields($list);
        //$this->addServiceSystemsListFields($list);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show->add('name')
            ->add('shortName');
        $this->addOrganisationOneToOneShowFields($show);
        $this->addServiceSystemsShowFields($show);
    }
}
