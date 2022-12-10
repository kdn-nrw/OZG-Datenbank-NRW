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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class ModelRegionProjectWebsiteAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        if (!$this->isExcludedFormField('modelRegionProject')) {
            $form
                ->add('modelRegionProject', ModelType::class, [
                    'label' => 'app.model_region_project_website.entity.model_region_project',
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ], [
                    'admin_code' => ModelRegionProjectAdmin::class
                ]);
        }
        $form
            ->add('name', TextType::class);
        $form
            ->add('url', UrlType::class, [
                'required' => false,
            ]);
        if (!$this->isExcludedFormField('description')) {
            $form->add('description', TextareaType::class, [
                'required' => false,
            ]);
        }
        $form
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $this->addDefaultDatagridFilter($filter, 'modelRegionProject');
        $filter->add('name');
        $filter->add('url');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('modelRegionProject', null, [
                'admin_code' => ModelRegionProjectAdmin::class
            ]);
        $list
            ->addIdentifier('name')
            ->add('url', 'url');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('modelRegionProject', null, [
                'admin_code' => ModelRegionProjectAdmin::class
            ])
            ->add('name')
            //->add('description')
            ->add('url', 'url');
    }
}
