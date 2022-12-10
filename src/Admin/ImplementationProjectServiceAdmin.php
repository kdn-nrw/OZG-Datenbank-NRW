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

use App\Entity\ImplementationStatus;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;


class ImplementationProjectServiceAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected function configureFormFields(FormMapper $form): void
    {
        if (!$this->isExcludedFormField('implementationProject')) {
            $form
                ->add('implementationProject', ModelAutocompleteType::class, [
                    'property' => ['name', 'description'],
                    'required' => true,
                ], [
                    'admin_code' => ImplementationProjectAdmin::class
                ]);
        }
        if (!$this->isExcludedFormField('service')) {
            $form
                ->add('service', ModelAutocompleteType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => ServiceAdmin::class
                ]);
        }
        $form
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => false,
                'choice_translation_domain' => false,
                'placeholder' => '',
            ])
            /*->add('description', TextareaType::class, [
                'required' => false,
            ])*/
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $this->addDefaultDatagridFilter($filter, 'implementationProject');
        $this->addDefaultDatagridFilter($filter, 'service');
        $filter->add('status');
        /*$filter->add('description');*/
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('implementationProject', null, [
                'admin_code' => ImplementationProjectAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'implementationProject'],
                ]
            ])
            ->add('service', null, [
                'admin_code' => ServiceAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'service'],
                ]
            ])
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => ImplementationStatus::class,
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
            ->add('implementationProject', null, [
                'admin_code' => ImplementationProjectAdmin::class
            ])
            ->add('service', null, [
                'admin_code' => ServiceAdmin::class
            ])
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                //'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
            ]);
    }
}
