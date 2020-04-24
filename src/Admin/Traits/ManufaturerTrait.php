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

use App\Admin\ManufacturerAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait ManufaturerTrait
{
    protected function addManufaturersFormFields(FormMapper $formMapper)
    {
        $formMapper->add('manufacturers', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addManufaturersDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('manufacturers',
            null, [
                'admin_code' => ManufacturerAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addManufaturersListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('manufacturers', null,[
                'admin_code' => ManufacturerAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addManufaturersShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('manufacturers', null,[
                'admin_code' => ManufacturerAdmin::class,
            ]);
    }
}