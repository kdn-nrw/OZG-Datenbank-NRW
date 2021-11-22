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
use App\Entity\MetaData\MetaItemProperty;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class MetaItemPropertyAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('general', [
                'label' => 'app.meta_item_property.groups.general',
                'class' => 'col-xs-12',
            ]);
        if (!$this->isExcludedFormField('parent')) {
            $formMapper
                ->add('parent', ModelType::class, [
                    'property' => 'internalLabel',
                    'required' => true,
                ], [
                    'admin_code' => MetaItemAdmin::class
                ]);
        }
        $subject = $this->getSubject();
        /** @var MetaItemProperty|null $subject */
        if (null !== $subject && $subject->getMetaType() !== MetaItemProperty::META_TYPE_FIELD) {
            $formMapper
                ->add('metaType', ChoiceType::class, [
                    'choices' => array_flip(MetaItemProperty::META_TYPES),
                    'attr' => [
                        'class' => 'form-control',
                        'data-sonata-select2' => 'false'
                    ],
                    'label' => false,
                    'required' => true,
                    'disabled' => true,
                ]);
        }
        $formMapper
            ->add('customLabel', TextType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'app.meta_item_property.entity.description',
                'required' => false,
                //'format' => 'richhtml',
                //'ckeditor_context' => 'default', // optional
            ]);
        //$formMapper->add('placeholder', TextType::class);
        $formMapper->end();
        $formMapper
            ->with('settings', [
                'label' => 'app.meta_item_property.groups.settings',
                'class' => 'col-xs-12',
            ])
            ->add('required', CheckboxType::class, [
                'required' => false,
            ])
            ->add('useForCompletenessCalculation', CheckboxType::class, [
                'required' => false,
            ])
            ->end();
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
