<?php

namespace App\Admin\Traits;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

trait AddressTrait
{
    protected function addAddressFormFields(FormMapper $formMapper)
    {
        $formMapper
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

    protected function addAddressDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('zipCode');
        $datagridMapper->add('town');
    }

    protected function addAddressListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('zipCode')
            ->add('town');
    }

    /**
     * @inheritdoc
     */
    public function addAddressShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('street')
            ->add('zipCode')
            ->add('town');
    }
}