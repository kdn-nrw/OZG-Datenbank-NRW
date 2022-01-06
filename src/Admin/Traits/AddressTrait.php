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

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

trait AddressTrait
{
    protected function addAddressFormFields(FormMapper $form)
    {
        $form
            ->add('street', TextType::class, [
                'required' => false,
            ])
            ->add('zipCode', TextType::class, [
                'required' => false,
            ])
            ->add('town', TextType::class, [
                'required' => false,
            ]);
    }

    protected function addAddressDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('zipCode');
        $filter->add('town');
    }

    protected function addAddressListFields(ListMapper $list)
    {
        $list
            ->add('zipCode')
            ->add('town');
    }

    /**
     * @inheritdoc
     */
    public function addAddressShowFields(ShowMapper $show)
    {
        $show
            ->add('street')
            ->add('zipCode')
            ->add('town');
    }
}