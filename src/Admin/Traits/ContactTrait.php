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

namespace App\Admin\Traits;

use App\Admin\ContactAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

trait ContactTrait
{
    protected function addContactsFormFields(
        FormMapper $form,
        $addOldField = false,
        $editable = false,
        $fieldName = 'contacts',
        $addTab = true,
        $required = false
    ): void
    {
        if ($editable) {
            if ($addTab) {
                $form
                    ->tab('app.contact.list')
                    ->with('app.contact.list', [
                        'label' => false,
                        'box_class' => 'box-tab',
                    ]);
            }
            $form->add($fieldName, CollectionType::class, [
                    //'label' => false,
                    'type_options' => [
                        'delete' => true,
                    ],
                    'by_reference' => false,
                    'required' => $required,
                ], [
                    'admin_code' => ContactAdmin::class,
                    'edit' => 'inline',
                    'inline' => 'natural',
                    //'sortable' => 'position',
                    'ba_custom_exclude_fields' => ['organisationEntity', 'organisation', 'contactType',],
                ]);

            if ($addOldField) {
                $form->add('contact', TextareaType::class, [
                    'required' => $required,
                ]);
            }
            if ($addTab) {
                $form->end();
                $form->end();
            }
        } else {
            $form->add($fieldName, ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => $required,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
            ]);
            if ($addOldField) {
                $form->add('contact', TextareaType::class, [
                    'required' => $required,
                ]);
            }
        }
    }

    protected function addContactsListFields(ListMapper $list, $fieldName = 'contacts'): void
    {
        $list
            ->add($fieldName, null, [
                'admin_code' => ContactAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addContactsShowFields(ShowMapper $show, $addOldField = false, $fieldName = 'contacts'): void
    {
        $show
            ->add($fieldName, null, [
                'admin_code' => ContactAdmin::class,
            ]);
        if ($addOldField) {
            $show->add('contact');
        }
    }
}