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
    /**
     * Add the address form fields
     * @param FormMapper $form
     * @return void
     */
    protected function addAddressFormFields(FormMapper $form): void
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

    /**
     * Add the address filter fields
     * @param DatagridMapper $filter
     * @return void
     */
    protected function addAddressDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('zipCode');
        $filter->add('town');
    }

    /**
     * Add the address list fields
     * @param ListMapper $list
     * @return void
     */
    protected function addAddressListFields(ListMapper $list): void
    {
        $list
            ->add('zipCode')
            ->add('town');
    }

    /**
     * Add the address show fields
     * @param ShowMapper $show
     */
    public function addAddressShowFields(ShowMapper $show): void
    {
        $show
            ->add('street')
            ->add('zipCode')
            ->add('town');
    }
}