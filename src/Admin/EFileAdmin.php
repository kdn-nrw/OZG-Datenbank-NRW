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

use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
use App\Entity\EFileStatus;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class EFileAdmin extends AbstractAppAdmin implements ExtendedSearchAdminInterface, EnableFullTextSearchAdminInterface
{
    use CommuneTrait;
    use SpecializedProcedureTrait;

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addCommunesFormFields($form);
        $form
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
        $this->addSpecializedProceduresFormFields($form);
        $form
            ->add('leadingSystem', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ]);
        $form->add('storageTypes', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
        $form
            ->add('notes', SimpleFormatterType::class, [
                'label' => 'app.efile.entity.notes_placeholder',
                'format' => 'richhtml',
                'required' => false,
                'ckeditor_context' => 'default', // optional
            ])
            ->add('hasEconomicViabilityAssessment', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ]);
        $form
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
                'required' => false,
            ]);
        $form
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'communes');
        $this->addDefaultDatagridFilter($filter, 'serviceProvider');
        $this->addDefaultDatagridFilter($filter,'leadingSystem');
        $this->addDefaultDatagridFilter($filter, 'specializedProcedures');
        $this->addDefaultDatagridFilter($filter, 'storageTypes');
        $filter->add('hasEconomicViabilityAssessment');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
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
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => EFileStatus::class,
                'catalogue' => 'messages',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'status'],
                ]
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name')
            ->add('description');
        $this->addCommunesShowFields($show);
        $show
            ->add('serviceProvider')
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => EFileStatus::class,
                'catalogue' => 'messages',
            ])
            ->add('url', 'url')
            ->add('leadingSystem');
        $this->addSpecializedProceduresShowFields($show);
        $show->add('specializedProcedures.manufacturers', null, [
            'label' => 'app.specialized_procedure.entity.manufacturers',
            'admin_code' => ManufacturerAdmin::class,
            'template' => 'General/show-specialized-procedures-manufacturers.html.twig',
        ]);
        $show
            ->add('storageTypes', null, [
                'admin_code' => EFileStorageTypeAdmin::class,
            ]);
        $show
            ->add('notes', FieldDescriptionInterface::TYPE_HTML)
            ->add('hasEconomicViabilityAssessment')
            ->add('sumInvestments', 'currency', [
                'currency' => 'EUR',
            ])
            ->add('followUpCosts', 'currency', [
                'currency' => 'EUR',
            ])
            ->add('savingPotentialNotes', FieldDescriptionInterface::TYPE_HTML);
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

    /**
     * Returns the classname label.
     *
     * @return string the classname label
     */
    public function getClassnameLabel(): string
    {
        return 'efile';
    }
}
