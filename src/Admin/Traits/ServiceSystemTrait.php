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

use App\Admin\ServiceSystemAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait ServiceSystemTrait
{
    protected function addServiceSystemsFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('serviceSystems', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ],
                [
                    'admin_code' => ServiceSystemAdmin::class,
                ]
            );
    }

    protected function addServiceSystemsListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('serviceSystems', null,[
                'admin_code' => ServiceSystemAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addServiceSystemsShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('serviceSystems', null,[
                'admin_code' => ServiceSystemAdmin::class,
            ]);
    }
}