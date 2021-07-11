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

namespace App\Admin\MetaData;

use App\Admin\AbstractAppAdmin;
use App\Entity\FederalInformationManagementType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class MetaItemAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('app.meta_item.groups.general_data', ['class' => 'col-xs-12'])
            ->add('metaKey', ChoiceType::class, [
                'choices' => array_flip(FederalInformationManagementType::$mapTypes),
                'attr' => [
                    'class' => 'form-control',
                    'data-sonata-select2' => 'false'
                ],
                'label' => false,
                'required' => true,
                'disabled' => true,
            ])
            ->add('customLabel', TextType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'app.meta_item_property.entity.description',
                'required' => false,
                //'format' => 'richhtml',
                //'ckeditor_context' => 'default', // optional
            ]);
        $formMapper->end();
        $formMapper->with('app.meta_item.groups.property_data', ['class' => 'col-xs-12']);
        $formMapper->add('metaItemProperties', CollectionType::class, [
            'label' => false,
            'type_options' => [
                'delete' => false,
            ],
            'btn_add' => false,
            'required' => true,
        ], [
            'admin_code' => MetaItemPropertyAdmin::class,
            'edit' => 'inline',
            'inline' => 'natural',
            'ba_custom_exclude_fields' => ['parent'],
        ]);
        $formMapper->end();
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('metaKey', 'string', [
                'template' => 'MetaData/list-meta-key.html.twig',
            ])
            ->add('customLabel')
            ->add('description');
        $listMapper->add('_action', null, [
            'label' => 'app.common.actions',
            'translation_domain' => 'messages',
            'actions' => [
                'show' => [],
                'edit' => [],
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('metaType')
            ->add('metaKey')
            ->add('customLabel')
            ->add('description');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
    }
}
