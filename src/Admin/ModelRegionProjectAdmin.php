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

namespace App\Admin;

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\DatePickerTrait;
use App\Admin\Traits\ModelRegionTrait;
use App\Admin\Traits\OrganisationTrait;
use App\Admin\Traits\SolutionTrait;
use App\Entity\ModelRegionProject;
use App\Entity\ModelRegionProjectDocument;
use App\Form\Type\ModelRegionDocumentType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ModelRegionProjectAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use AddressTrait;
    use DatePickerTrait;
    use ModelRegionTrait;
    use OrganisationTrait;
    use SolutionTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('default', ['label' => 'app.model_region_project.tabs.default']);
        $formMapper->with('general', [
            'label' => 'app.model_region_project.group.general_data',
            'class' => 'col-xs-12 col-md-6',
        ]);
        $formMapper
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addDatePickerFormField($formMapper, 'projectStartAt');
        $this->addDatePickerFormField($formMapper, 'projectEndAt', 20);
        $this->addOrganisationsFormFields($formMapper);
        $formMapper->end();
        $formMapper->with('characteristics', [
            'label' => 'app.model_region_project.group.characteristics',
            'class' => 'col-xs-12 col-md-6',
        ]);
        $formMapper
            ->add('usp', TextareaType::class, [
                'required' => false,
            ])
            ->add('communesBenefits', TextareaType::class, [
                'required' => false,
            ])
            ->add('transferableService', TextareaType::class, [
                'required' => false,
            ])
            ->add('transferableStart', TextareaType::class, [
                'required' => false,
            ]);
        $formMapper->end();
        $formMapper->with('reference_data', [
            'label' => 'app.model_region_project.group.reference_data',
            'class' => 'clear-left-md col-xs-12 col-md-6',
        ]);
        $this->addModelRegionsFormFields($formMapper);
        $this->addSolutionsFormFields($formMapper);
        $formMapper->end();
        $formMapper->with('documents', [
            'label' => 'app.model_region_project.group.documents',
            'class' => 'col-xs-12 col-md-6',
        ]);
        $formMapper->add('documents', CollectionType::class, [
            'label' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'entry_type' => ModelRegionDocumentType::class,
            'entry_options' => [
                'parent_admin' => $this,
            ],
        ]);
        $formMapper->end();
        $formMapper->end();
    }

    public function preUpdate($object)
    {
        $this->cleanDocuments($object);
    }

    public function prePersist($object)
    {
        $this->cleanDocuments($object);
    }

    public function cleanDocuments($object)
    {
        /** @var ModelRegionProject $object */
        $removeDocuments = $object->cleanDocuments();

        if (!empty($removeDocuments)) {
            /** @var ModelManager $modelManager */
            $modelManager = $this->getModelManager();
            $docEm = $modelManager->getEntityManager(ModelRegionProjectDocument::class);
            foreach ($removeDocuments as $document) {
                if ($docEm->contains($document)) {
                    $docEm->remove($document);
                }
            }
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addDefaultDatagridFilter($datagridMapper, 'organisations');
        $this->addDefaultDatagridFilter($datagridMapper, 'projectStartAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'projectEndAt');
        $datagridMapper
            ->add('description')
            ->add('usp')
            ->add('communesBenefits')
            ->add('transferableService')
            ->add('transferableStart');
        $this->addDefaultDatagridFilter($datagridMapper, 'modelRegions');
        $this->addDefaultDatagridFilter($datagridMapper, 'solutions');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $this->addDatePickersListFields($listMapper, 'projectStartAt');
        $this->addDatePickersListFields($listMapper, 'projectEndAt');
        $this->addOrganisationsListFields($listMapper);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('description');
        $this->addDatePickersShowFields($showMapper, 'projectStartAt');
        $this->addDatePickersShowFields($showMapper, 'projectEndAt');
        $showMapper
            ->add('usp')
            ->add('communesBenefits')
            ->add('transferableService')
            ->add('transferableStart');
        $this->addOrganisationsShowFields($showMapper);
        $this->addModelRegionsShowFields($showMapper);
        $showMapper->add('documents', null, [
            'template' => 'General/Show/show-attachments.html.twig',
        ]);
        $showMapper
            ->add('solutions', null, [
                'admin_code' => SolutionAdmin::class,
                'route' => [
                    'name' => 'edit',
                ],
                'showServices' => true,
            ]);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('download', $this->getRouterIdParameter() . '/download');
    }
}
