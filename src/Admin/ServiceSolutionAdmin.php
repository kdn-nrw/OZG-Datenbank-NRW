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
use App\Entity\ServiceSolution;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;


class ServiceSolutionAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isExcludedFormField('service')) {
            $formMapper
                ->add('service', ModelAutocompleteType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => \App\Admin\ServiceAdmin::class
                ]);
        }
        if (!$this->isExcludedFormField('solution')) {
            $formMapper
                ->add('solution', ModelAutocompleteType::class, [
                    'property' => ['name', 'description'],
                    'required' => true,
                ], [
                    'admin_code' => \App\Admin\SolutionAdmin::class
                ]);
        }
        $formMapper/*
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addDefaultDatagridFilter($datagridMapper, 'service');
        $this->addDefaultDatagridFilter($datagridMapper, 'solution');
        /*$datagridMapper->add('description');
        $datagridMapper->add('status');*/
        $datagridMapper->add('maturity');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('service', null, [
                'admin_code' => \App\Admin\ServiceAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => \App\Admin\SolutionAdmin::class
            ])
            /*->add('description')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])*/
            ->add('maturity');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('service', null, [
                'admin_code' => \App\Admin\ServiceAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => \App\Admin\SolutionAdmin::class
            ])
            ->add('maturity')/*
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])*/
        ;
    }

    public function getNewInstance()
    {
        /** @var ServiceSolution $object */
        $object = parent::getNewInstance();
        $defaultMaturity = $this->getModelManager()->find(Maturity::class, Maturity::DEFAULT_ID);
        if (null !== $defaultMaturity) {
            /** @var Maturity $defaultMaturity */
            $object->setMaturity($defaultMaturity);
        }

        return $object;
    }
}
