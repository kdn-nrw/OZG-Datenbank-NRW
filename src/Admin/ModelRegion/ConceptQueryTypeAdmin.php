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

namespace App\Admin\ModelRegion;

use App\Admin\AbstractAppAdmin;
use App\Entity\ModelRegion\ConceptQueryType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ConceptQueryTypeAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('queryGroup', ChoiceType::class, [
                'choices' => array_flip(ConceptQueryType::getTypeLabelMap(true)),
                'attr' => [
                    'class' => 'form-control',
                    'data-sonata-select2' => 'false'
                ],
                'label' => false,
                'required' => true,
                //'disabled' => true,
            ])
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('choicesText', TextareaType::class, [
                'required' => false,
            ])
            ->add('placeholder', TextareaType::class, [
                'required' => false,
            ])
            ->add('position', IntegerType::class);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $filter
            ->add('queryGroup', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => array_flip(ConceptQueryType::getTypeLabelMap(true)),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    //'choice_translation_domain' => 'SonataAdminBundle',
                ],
                'field_type' => ChoiceType::class,
            ]);
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('queryGroup', 'choice', [
                'editable' => false,
                'choices' => ConceptQueryType::getTypeLabelMap(true),
                'catalogue' => 'messages',
            ])
            ->add('name');
        $list->add('_action', null, [
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
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('queryGroup', 'choice', [
                'editable' => false,
                'choices' => ConceptQueryType::getTypeLabelMap(true),
                'catalogue' => 'messages',
            ])
            ->add('name')
            ->add('description');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        //$collection->remove('create');
        //$collection->remove('delete');
    }
}
