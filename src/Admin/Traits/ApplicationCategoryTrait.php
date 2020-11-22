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

namespace App\Admin\Traits;

use App\Admin\ApplicationCategoryAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;

trait ApplicationCategoryTrait
{
    protected function addApplicationCategoriesFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('categories', ModelAutocompleteType::class,
                [
                    'property' => 'name',
                    'required' => false,
                    'multiple' => true,
                ],
                [
                    'admin_code' => ApplicationCategoryAdmin::class,
                ]
            );
    }

    protected function addApplicationCategoriesListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('categories', null, [
                'admin_code' => ApplicationCategoryAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addApplicationCategoriesShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('categories', null, [
                'admin_code' => ApplicationCategoryAdmin::class,
                //'template' => 'General/Show/show-categories.twig',
            ]);
    }
}