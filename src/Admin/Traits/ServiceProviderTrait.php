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

use App\Admin\StateGroup\ServiceProviderAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait ServiceProviderTrait
{
    protected function addServiceProvidersFormFields(FormMapper $formMapper)
    {
        $formMapper->add('serviceProviders', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addServiceProvidersDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('serviceProviders',
            null, [
                'admin_code' => ServiceProviderAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addServiceProvidersListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('serviceProviders', null,[
                'admin_code' => ServiceProviderAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addServiceProvidersShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('serviceProviders', null,[
                'admin_code' => ServiceProviderAdmin::class,
            ]);
    }
}