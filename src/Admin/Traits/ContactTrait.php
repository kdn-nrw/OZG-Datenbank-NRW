<?php

namespace App\Admin\Traits;

use App\Admin\ContactAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

trait ContactTrait
{
    protected function addContactsFormFields(FormMapper $formMapper, $addOldField = false, $editable = false, $fieldName = 'contacts')
    {
        if ($editable) {
            $formMapper
                ->tab('app.contact.list')
                ->with('app.contact.list', [
                    'label' => false,
                    'box_class' => 'box-tab',
                ])
                    ->add($fieldName, CollectionType::class, [
                        //'label' => false,
                        'type_options' => [
                            'delete' => true,
                        ],
                        'by_reference' => false,
                    ], [
                        'admin_code' => ContactAdmin::class,
                        'edit' => 'inline',
                        'inline' => 'natural',
                        //'sortable' => 'position',
                        'ba_custom_hide_fields' => ['contactType'],
                    ]);

            if ($addOldField) {
                $formMapper->add('contact', TextareaType::class, [
                    'required' => false,
                ]);
            }
            $formMapper->end();
            $formMapper->end();
        } else {
            $formMapper->add($fieldName, ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
            ]);
            if ($addOldField) {
                $formMapper->add('contact', TextareaType::class, [
                    'required' => false,
                ]);
            }
        }
    }

    protected function addContactsDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('contacts',
            null, [
                'admin_code' => ContactAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addContactsListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('contacts', null, [
                'admin_code' => ContactAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addContactsShowFields(ShowMapper $showMapper, $addOldField = false)
    {
        $showMapper
            ->add('contacts', null, [
                'admin_code' => ContactAdmin::class,
            ]);
        if ($addOldField) {
            $showMapper->add('contact');
        }
    }
}