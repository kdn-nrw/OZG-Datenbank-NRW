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

use App\Admin\ServiceAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;

trait ServiceTrait
{
    protected function addServicesFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('services', ModelAutocompleteType::class, [
                    'property' => 'name',
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                ],
                [
                    'admin_code' => ServiceAdmin::class,
                ]
            );
    }

    protected function addServicesDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('services',
            null, [
                'admin_code' => ServiceAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addServicesListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('services', null,[
                'admin_code' => ServiceAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addServicesShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('services', null,[
                'admin_code' => ServiceAdmin::class,
                //'template' => 'General/Show/show-services.twig',
            ]);
    }
}