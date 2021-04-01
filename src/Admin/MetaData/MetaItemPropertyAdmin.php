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
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class MetaItemPropertyAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $hideFields = $this->getFormHideFields();
        if (!in_array('parent', $hideFields, false)) {
            $formMapper
                ->add('parent', ModelType::class, [
                    'property' => 'internalLabel',
                    'required' => true,
                ], [
                    'admin_code' => MetaItemAdmin::class
                ]);
        }
        $formMapper
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
