<?php

namespace App\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ManufacturerAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.manufacturer.';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, ['label' => $this->labelGroup . 'name'])
            ->add('street', TextType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'street'
            ])
            ->add('zipCode', TextType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'zip_code'
            ])
            ->add('town', TextType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'town'
            ])
            ->add('url', TextType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'url'
            ])
            ->add('specializedProcedures', ModelType::class, [
                'label' => $this->labelGroup . 'specialized_procedures',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name',
            null,
            ['label' => $this->labelGroup . 'name']
        );
        $datagridMapper->add('zipCode',
            null,
            ['label' => $this->labelGroup . 'zip_code']
        );
        $datagridMapper->add('town',
            null,
            ['label' => $this->labelGroup . 'town']
        );
        $datagridMapper->add('specializedProcedures',
            null,
            ['label' => $this->labelGroup . 'specialized_procedures'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('specializedProcedures', null, [
                'label' => $this->labelGroup . 'specialized_procedures',
            ])
            ->add('url', null, [
                'label' => $this->labelGroup . 'url',
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('street', null, [
                'label' => $this->labelGroup . 'street',
            ])
            ->add('zipCode', null, [
                'label' => $this->labelGroup . 'zip_code',
            ])
            ->add('town', null, [
                'label' => $this->labelGroup . 'town',
            ])
            ->add('url', null, [
                'label' => $this->labelGroup . 'url',
            ])
            ->add('specializedProcedures', null, [
                'label' => $this->labelGroup . 'specialized_procedures',
            ]);
    }
}
