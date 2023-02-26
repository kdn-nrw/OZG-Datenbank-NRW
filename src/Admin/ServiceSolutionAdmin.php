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

use App\Entity\Maturity;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;


class ServiceSolutionAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected function configureFormFields(FormMapper $form): void
    {
        if (!$this->isExcludedFormField('service')) {
            $form
                ->add('service', ModelAutocompleteType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => ServiceAdmin::class
                ]);
        }
        if (!$this->isExcludedFormField('solution')) {
            $form
                ->add('solution', ModelAutocompleteType::class, [
                    'property' => ['name', 'description'],
                    'required' => true,
                ], [
                    'admin_code' => SolutionAdmin::class
                ]);
        }
        $form/*
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
                'choice_translation_domain' => false,
            ])*/
        ->add('maturity', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'choice_translation_domain' => false,
        ])
            /*->add('description', TextareaType::class, [
                'required' => false,
            ])*/
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $this->addDefaultDatagridFilter($filter, 'service');
        $this->addDefaultDatagridFilter($filter, 'solution');
        /*$filter->add('description');
        $filter->add('status');*/
        $filter->add('maturity');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('service', null, [
                'admin_code' => ServiceAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => SolutionAdmin::class
            ])
            /*->add('description')
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])*/
            ->add('maturity');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('service', null, [
                'admin_code' => ServiceAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => SolutionAdmin::class
            ])
            ->add('maturity')/*
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])*/
        ;
    }

    /**
     * @inheritDoc
     */
    protected function alterNewInstance(object $object): void
    {
        $defaultMaturity = $this->getModelManager()->find(Maturity::class, Maturity::DEFAULT_ID);
        if (null !== $defaultMaturity) {
            /** @var Maturity $defaultMaturity */
            $object->setMaturity($defaultMaturity);
        }
    }
}
