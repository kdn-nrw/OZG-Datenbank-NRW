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

use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ManufaturerTrait;
use App\Admin\Traits\ServiceProviderTrait;
use App\Entity\ApplicationCategory;
use App\Entity\SpecializedProcedure;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SpecializedProcedureAdmin extends AbstractAppAdmin
{
    use CommuneTrait;
    use ManufaturerTrait;
    use ServiceProviderTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class);
        $this->addManufaturersFormFields($formMapper);
        $formMapper
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addServiceProvidersFormFields($formMapper);
        $this->addCommunesFormFields($formMapper);
        $formMapper->end();
    }

    public function postUpdate($object)
    {
        /** @var SpecializedProcedure $object */
        $this->updateApplicationCategory($object);
    }

    public function postPersist($object)
    {
        /** @var SpecializedProcedure $object */
        $this->updateApplicationCategory($object);
    }

    public function postRemove($object)
    {
        /** @var SpecializedProcedure $object */
        $this->updateApplicationCategory($object, true);
    }

    /**
     * Create or update the category for the given entity
     * @param SpecializedProcedure $object The updated object
     * @param bool $remove Remove entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function updateApplicationCategory(SpecializedProcedure $object, bool $remove = false)
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->getModelManager();
        $em = $modelManager->getEntityManager(ApplicationCategory::class);
        $repository = $em->getRepository(ApplicationCategory::class);
        $category = $repository->findOneBy(['importId' => $object->getId(), 'importSource' => get_class($object)]);
        $hasChanges = false;
        if ($remove) {
            if (null !== $category) {
                $em->remove($category);
                $em->flush($category);
            }
        } elseif (null === $category) {
            $category = new ApplicationCategory();
            $category->setName($object->getName());
            $category->setImportId($object->getId());
            $category->setImportSource(get_class($object));
            $category->setHidden($object->isHidden());
            $parent = $repository->find(SpecializedProcedure::DEFAULT_PARENT_APPLICATION_CATEGORY_ID);
            if (null !== $parent) {
                $category->setParent($parent);
            }
            $modelManager->create($category);
            $em->persist($category);
            $em->flush($category);
        } elseif ($object->isHidden() !== $category->isHidden()) {
            $category->setHidden($object->isHidden());
            $modelManager->update($category);
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addFullTextDatagridFilter($datagridMapper);
        $datagridMapper->add('name');
        $this->addManufaturersDatagridFilters($datagridMapper);
        $this->addCommunesDatagridFilters($datagridMapper);
        $this->addServiceProvidersDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name');
        $this->addManufaturersListFields($listMapper);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description');
        $this->addManufaturersShowFields($showMapper);
        $this->addServiceProvidersShowFields($showMapper);
        $this->addCommunesShowFields($showMapper);
    }
}
