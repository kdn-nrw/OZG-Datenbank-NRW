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
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;

trait ServiceTrait
{
    protected function addServicesFormFields(FormMapper $form)
    {
        $form
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

    protected function addServicesListFields(ListMapper $list)
    {
        $list
            ->add('services', null,[
                'admin_code' => ServiceAdmin::class,
            ]);
    }

    /**
     * Add service fields for show action
     * @param ShowMapper $show
     * @param array $overrideFieldDescriptionOptions
     */
    public function addServicesShowFields(ShowMapper $show, array $overrideFieldDescriptionOptions = [])
    {
        $fieldDescriptionOptions = array_merge([
            'admin_code' => ServiceAdmin::class,
            'showFimTypes' => false,
        ], $overrideFieldDescriptionOptions);
        $show
            ->add('services', null, $fieldDescriptionOptions);
    }
}