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

namespace App\Admin\ModelRegion;

use App\Admin\AbstractAppAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ModelRegionProjectConceptQueryAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        if (!$this->isExcludedFormField('modelRegionProject')) {
            $form
                ->add('modelRegionProject', ModelType::class, [
                    'label' => 'app.model_region_project_concept_query.entity.model_region_project',
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ], [
                    'admin_code' => ModelRegionProjectAdmin::class
                ]);
        }
        $form
            ->add('conceptQueryType', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
                'disabled' => true,
            ], [
                    'admin_code' => ConceptQueryTypeAdmin::class,
                ]
            );
        $form->add('description', TextareaType::class, [
            'required' => false,
        ]);
        $form
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $this->addDefaultDatagridFilter($filter, 'modelRegionProject');
        $this->addDefaultDatagridFilter($filter, 'conceptQueryType');
        $filter->add('description');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('modelRegionProject', null, [
                'admin_code' => ModelRegionProjectAdmin::class
            ])
            ->add('conceptQueryType', null, [
                'admin_code' => ConceptQueryTypeAdmin::class
            ]);
        $list
            ->addIdentifier('description');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('modelRegionProject', null, [
                'admin_code' => ModelRegionProjectAdmin::class
            ])
            ->add('conceptQueryType', null, [
                'admin_code' => ConceptQueryTypeAdmin::class
            ])
            ->add('description');
    }
}
