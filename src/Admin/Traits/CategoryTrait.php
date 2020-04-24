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

namespace App\Admin\Traits;

use App\Admin\CategoryAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait CategoryTrait
{
    protected function addCategoriesFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('categories', ModelType::class,
                [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ],
                [
                    'admin_code' => CategoryAdmin::class,
                ]
            );
    }

    protected function addCategoriesDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('categories',
            null, [
                'admin_code' => CategoryAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addCategoriesListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('categories', null, [
                'admin_code' => CategoryAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addCategoriesShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('categories', null, [
                'admin_code' => CategoryAdmin::class,
                //'template' => 'General/Show/show-categories.twig',
            ]);
    }
}