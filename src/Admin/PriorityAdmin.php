<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class PriorityAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.priority.';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, [
                'label' => $this->labelGroup . 'name'
            ])
            ->add('level', IntegerType::class, [
                'label' => $this->labelGroup . 'level'
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
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->addIdentifier('level', null, [
                'label' => $this->labelGroup . 'level',
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
            ->add('level', null, [
                'label' => $this->labelGroup . 'level',
            ])
            ->add('description', null, [
                'label' => $this->labelGroup . 'description',
            ]);
    }
}
