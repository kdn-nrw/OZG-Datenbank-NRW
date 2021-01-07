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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;


class ImplementationProjectServiceAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $hideFields = $this->getFormHideFields();
        if (!in_array('implementationProject', $hideFields, false)) {
            $formMapper
                ->add('implementationProject', ModelAutocompleteType::class, [
                    'property' => ['name', 'description'],
                    'required' => true,
                ], [
                    'admin_code' => \App\Admin\ImplementationProjectAdmin::class
                ]);
        }
        if (!in_array('service', $hideFields, false)) {
            $formMapper
                ->add('service', ModelAutocompleteType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => \App\Admin\ServiceAdmin::class
                ]);
        }
        $formMapper
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addDefaultDatagridFilter($datagridMapper, 'implementationProject');
        $this->addDefaultDatagridFilter($datagridMapper, 'service');
        $datagridMapper->add('status');
        /*$datagridMapper->add('description');*/
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('implementationProject', null, [
                'admin_code' => \App\Admin\ImplementationProjectAdmin::class
            ])
            ->add('service', null, [
                'admin_code' => \App\Admin\ServiceAdmin::class
            ])
            ->add('status', 'choice', [
                'editable' => true,
                'class' => ImplementationStatus::class,
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
            ->add('implementationProject', null, [
                'admin_code' => \App\Admin\ImplementationProjectAdmin::class
            ])
            ->add('service', null, [
                'admin_code' => \App\Admin\ServiceAdmin::class
            ])
            ->add('status', 'choice', [
                'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
    }
}
