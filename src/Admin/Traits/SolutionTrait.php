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

use App\Admin\SolutionAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait SolutionTrait
{
    protected function addSolutionsFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('solutions', ModelType::class,
                [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ],
                [
                    'admin_code' => SolutionAdmin::class,
                ]
            );
    }

    protected function addSolutionsListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('solutions', null, [
                'admin_code' => SolutionAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addSolutionsShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('solutions', null, [
                'admin_code' => SolutionAdmin::class,
                'template' => 'General/Show/show-solutions.html.twig',
            ]);
    }
}