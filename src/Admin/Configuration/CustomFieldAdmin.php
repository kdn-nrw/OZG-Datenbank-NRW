<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Configuration;

use App\Admin\AbstractAppAdmin;
use App\Entity\Configuration\HasCustomFieldsEntityInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class CustomFieldAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class)
            ->add('fieldLabel', TextType::class)
            ->add('hidden', CheckboxType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                //'format' => 'richhtml',
                //'ckeditor_context' => 'default', // optional
            ])
            ->add('recordType', ChoiceType::class, [
                'choices' => array_flip($this->getRecordTypes()),
                'required' => true,
                'expanded' => false,
            ])
            ->add('fieldType', ChoiceType::class, [
                'choices' => array_flip($this->getFieldTypes()),
                'required' => true,
                'expanded' => false,
            ])
            ->add('fieldOptions', TextareaType::class, [
                'required' => false,
            ])->add('required', CheckboxType::class, [
                'required' => false,
            ]);

        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('fieldLabel')
            ->add('recordType', 'choice', [
                'editable' => false,
                'choices' => $this->getRecordTypes(),
                'catalogue' => 'messages',
            ]);
        $listMapper->add('_action', null, [
            'label' => 'app.common.actions',
            'translation_domain' => 'messages',
            'actions' => [
                'edit' => [],
            ]
        ]);
    }

    protected function getRecordTypes(): array
    {
        $choices = [];
        $pool = $this->getConfigurationPool();
        if (null !== $pool) {
            $classNames = $pool->getAdminClasses();
            foreach ($classNames as $className => $adminServiceIds) {
                if (null !== $admin = $pool->getAdminByAdminCode(current($adminServiceIds))) {
                    if (is_a($admin->getClass(), HasCustomFieldsEntityInterface::class, true)) {
                        $choices[$admin->getClass()] = $admin->getClassnameLabel();
                    }
                }
            }
        }
        return $choices;
    }

    protected function getFieldTypes(): array
    {
        return [
            TextType::class => 'app.custom_field.entity.field_type_choices.text',
            TextareaType::class => 'app.custom_field.entity.field_type_choices.textarea',
            IntegerType::class => 'app.custom_field.entity.field_type_choices.integer',
        ];
    }
}
