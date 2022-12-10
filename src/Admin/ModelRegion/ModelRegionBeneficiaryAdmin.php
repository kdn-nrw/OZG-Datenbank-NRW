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

namespace App\Admin\ModelRegion;

use App\Admin\AbstractAppAdmin;
use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\Traits\AddressTrait;
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


class ModelRegionBeneficiaryAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use ContactTrait;
    use AddressTrait;
    use OrganisationOneToOneTrait;

    protected $baseRoutePattern = 'model-region/beneficiary';

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.model_region_beneficiary.entity.organisation_contacts' => 'app.organisation.entity.contacts',
        'app.model_region_beneficiary.entity.organisation_url' => 'app.organisation.entity.url',
        'app.model_region_beneficiary.entity.organisation_street' => 'app.organisation.entity.street',
        'app.model_region_beneficiary.entity.organisation_zip_code' => 'app.organisation.entity.zip_code',
        'app.model_region_beneficiary.entity.organisation_town' => 'app.organisation.entity.town',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->tab('default', ['label' => 'app.model_region_beneficiary.tabs.default']);
        $form->with('general', [
            'label' => 'app.model_region_beneficiary.tabs.default',
        ]);
        $this->addOrganisationOneToOneFormFields($form);
        $form->add('shortName', TextType::class, [
            'required' => false,
        ]);
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

    private function updateOrganisation(OrganisationEntityInterface $object): void
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
        $this->addDefaultDatagridFilter($filter, 'organisation.modelRegionProjects');
        $this->addOrganisationOneToOneDatagridFilters($filter);
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
            ->add('shortName')
            ->add('modelRegionProjects', null, [
                'admin_code' => ModelRegionProjectAdmin::class,
            ]);
        $this->addOrganisationOneToOneShowFields($show);
    }
}
