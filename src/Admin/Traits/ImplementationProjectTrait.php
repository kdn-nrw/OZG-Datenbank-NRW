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

use App\Admin\ImplementationProjectAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait ImplementationProjectTrait
{
    protected function addImplementationProjectsFormFields(FormMapper $form)
    {
        $form
            ->add('implementationProjects', ModelType::class,
                [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ],
                [
                    'admin_code' => ImplementationProjectAdmin::class,
                ]
            );
    }

    protected function addImplementationProjectsListFields(ListMapper $list)
    {
        $list
            ->add('implementationProjects', null, [
                'admin_code' => ImplementationProjectAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addImplementationProjectsShowFields(ShowMapper $show)
    {
        $show
            ->add('implementationProjects', null, [
                'admin_code' => ImplementationProjectAdmin::class,
                //'template' => 'General/Show/show-implementationProjects.html.twig',
            ]);
    }
}