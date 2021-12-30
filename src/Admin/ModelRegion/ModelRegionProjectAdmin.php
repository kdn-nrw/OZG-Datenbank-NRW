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
use App\Admin\OrganisationAdmin;
use App\Admin\SolutionAdmin;
use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\DatePickerTrait;
use App\Admin\Traits\ModelRegionTrait;
use App\Admin\Traits\OrganisationTrait;
use App\Admin\Traits\SluggableTrait;
use App\Admin\Traits\SolutionTrait;
use App\Entity\ModelRegion\ModelRegionProject;
use App\Entity\ModelRegion\ModelRegionProjectDocument;
use App\Exporter\Source\ManySolutionsValueFormatter;
use App\Exporter\Source\ServiceSolutionValueFormatter;
use App\Form\Type\ModelRegionDocumentType;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
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
    use SluggableTrait;

    protected $baseRoutePattern = 'model-region/project';

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
        $formMapper
            ->add('categories', ModelType::class,
                [
                    'btn_add' => 'app.common.model_list_type.add',
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                    'btn_catalogue' => 'messages',
                ],
                [
                    'admin_code' => ModelRegionProjectCategoryAdmin::class,
                ]
            );
        $this->addOrganisationsFormFields($formMapper);

        $formMapper->add('websites', \Sonata\Form\Type\CollectionType::class, [
            'type_options' => [
                'delete' => true,
            ],
            'by_reference' => false,
        ], [
            'edit' => 'inline',
            'inline' => 'table',
            'ba_custom_exclude_fields' => ['modelRegionProject', 'description'],
        ]);
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
        $this->addSlugFormField($formMapper, $this->getSubject());
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
        $this->addDefaultDatagridFilter($datagridMapper, 'projectStartAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'projectEndAt');
        $this->addDefaultDatagridFilter($datagridMapper, 'categories');
        $this->addDefaultDatagridFilter($datagridMapper, 'organisations');
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
        $listMapper
            ->add('categories', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'categories'],
                ],
                'enable_filter_add' => true,
                'admin_code' => ModelRegionProjectCategoryAdmin::class,
            ]);
        $listMapper
            ->add('organisations', null, [
                'template' => 'General/Association/list_many_to_many_nolinks.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'organisations'],
                ],
                'enable_filter_add' => true,
                'admin_code' => OrganisationAdmin::class,
            ]);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $customServiceFormatter = new ManySolutionsValueFormatter();
        $customServiceFormatter->setDisplayType(ServiceSolutionValueFormatter::DISPLAY_SOLUTION_NAME);
        $customServiceFormatter->setShowServiceKeys(true);
        $settings->addCustomPropertyValueFormatter('solutions', $customServiceFormatter);
        $customServiceFormatter = new ManySolutionsValueFormatter();
        $customServiceFormatter->setDisplayType(ServiceSolutionValueFormatter::DISPLAY_SERVICE_KEY);
        $settings->setAdditionFields(['serviceSolutions',]);
        $settings->addCustomPropertyValueFormatter('serviceSolutions', $customServiceFormatter);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show->add('name')
            ->add('description');
        $this->addDatePickersShowFields($show, 'projectStartAt');
        $this->addDatePickersShowFields($show, 'projectEndAt');
        $show
            ->add('usp')
            ->add('communesBenefits')
            ->add('transferableService')
            ->add('transferableStart');
        $show
            ->add('categories', null, [
                'admin_code' => ModelRegionProjectCategoryAdmin::class,
                //'template' => 'General/Show/show-categories.twig',
            ]);
        $this->addOrganisationsShowFields($show);
        $this->addModelRegionsShowFields($show);
        $show
            ->add('websites', null, [
                'template' => 'ModelRegion/show-project-websites.html.twig',
            ]);
        $show->add('documents', null, [
            'template' => 'General/Show/show-attachments.html.twig',
        ]);
        $show
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
