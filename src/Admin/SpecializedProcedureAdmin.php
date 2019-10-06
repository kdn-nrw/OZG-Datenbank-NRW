<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SpecializedProcedureAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.specialized_procedure.';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, [
                'label' => $this->labelGroup . 'name'
            ])
            ->add('manufacturers', ModelType::class, [
                'label' => $this->labelGroup . 'manufacturers',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->labelGroup . 'description',
                'required' => false,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name',
            null,
            ['label' => $this->labelGroup . 'name']
        );
        $datagridMapper->add('manufacturers',
            null,
            ['label' => $this->labelGroup . 'manufacturers'],
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
            ->add('manufacturers', null, [
                'label' => $this->labelGroup . 'manufacturers',
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
            ->add('description', null, [
                'label' => $this->labelGroup . 'description',
            ])
            ->add('manufacturers', null, [
                'label' => $this->labelGroup . 'manufacturers',
            ]);
    }
}
