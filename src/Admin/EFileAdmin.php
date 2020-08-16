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

use App\Admin\Traits\SpecializedProcedureTrait;
use App\Entity\EFileStatus;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class EFileAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use SpecializedProcedureTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('serviceProvider', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
                'choice_translation_domain' => false,
            ])
            ->add('url', UrlType::class, [
                'required' => false
            ]);
        $this->addSpecializedProceduresFormFields($formMapper);
        $formMapper
            ->add('leadingSystem', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ]);
        $formMapper->add('storageTypes', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
        $formMapper
            ->add('notes', SimpleFormatterType::class, [
                'label' => 'app.efile.entity.notes_placeholder',
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ])
            ->add('hasEconomicViabilityAssessment', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ]);
        $formMapper
            ->add('sumInvestments', MoneyType::class, [
                'currency' => 'EUR',
                'required' => false,
            ])
            ->add('followUpCosts', MoneyType::class, [
                'currency' => 'EUR',
                'required' => false,
            ])
            ->add('savingPotentialNotes', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $formMapper
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addFullTextDatagridFilter($datagridMapper);
        $datagridMapper->add('name');
        $datagridMapper->add('serviceProvider',
            null,
            ['label' => 'app.efile.entity.service_provider'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('leadingSystem',
            null,
            ['label' => 'app.efile.entity.leading_system'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $this->addSpecializedProceduresDatagridFilters($datagridMapper);
        $datagridMapper->add('storageTypes',
            null, [
                'admin_code' => EFileStorageTypeAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('hasEconomicViabilityAssessment');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('serviceProvider', null, [
                'template' => 'General/Association/list_many_to_one_nolinks.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceProvider'],
                ]
            ])
            ->add('url', 'url')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => EFileStatus::class,
                'catalogue' => 'messages',
            ]);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description')
            ->add('serviceProvider')
            ->add('status', 'choice', [
                //'editable' => true,
                'class' => EFileStatus::class,
                'catalogue' => 'messages',
            ])
            ->add('url', 'url')
            ->add('leadingSystem');
        $this->addSpecializedProceduresShowFields($showMapper);
        $showMapper->add('specializedProcedures.manufacturers', null, [
            'label' => 'app.specialized_procedure.entity.manufacturers',
            'admin_code' => ManufacturerAdmin::class,
            'template' => 'General/show-specialized-procedures-manufacturers.html.twig',
        ]);
        $showMapper
            ->add('storageTypes', null, [
                'admin_code' => EFileStorageTypeAdmin::class,
            ]);
        $showMapper
            ->add('notes', 'html')
            ->add('hasEconomicViabilityAssessment')
            ->add('sumInvestments', 'currency', [
                'currency' => 'EUR',
            ])
            ->add('followUpCosts', 'currency', [
                'currency' => 'EUR',
            ])
            ->add('savingPotentialNotes', 'html');
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

    /**
     * Returns the classname label.
     *
     * @return string the classname label
     */
    public function getClassnameLabel()
    {
        return 'efile';
    }
}