<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class OfficeAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.office.';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, ['label' => $this->labelGroup . 'name'])
            ->add('url', TextType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'url'
            ])
            ->add('contact', TextareaType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'contact'
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
            ->add('name', null, [
                'label' => $this->labelGroup . 'name',
            ])
            ->add('url', null, [
                'label' => $this->labelGroup . 'url',
            ])
            ->add('contact', null, [
                'label' => $this->labelGroup . 'contact',
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
            ->add('url', null, [
                'label' => $this->labelGroup . 'url',
            ])
            ->add('contact', null, [
                'label' => $this->labelGroup . 'contact',
            ])
            ->add('description', null, [
                'label' => $this->labelGroup . 'description',
            ]);
    }
}
