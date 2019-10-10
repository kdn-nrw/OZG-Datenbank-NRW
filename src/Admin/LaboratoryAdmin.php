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
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class LaboratoryAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.laboratory.';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, [
                'label' => $this->labelGroup . 'name'
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->labelGroup . 'description',
                'required' => false,
            ])
            ->add('url', UrlType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'url'
            ])
            ->add('serviceProviders', ModelType::class, [
                'label' => $this->labelGroup . 'service_providers',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('participantsOther', TextareaType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'participants_other'
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name',
            null,
            ['label' => $this->labelGroup . 'name']
        );
        $datagridMapper->add('serviceProviders',
            null,
            ['label' => $this->labelGroup . 'service_providers'],
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
            ->add('url', 'url', [
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
            ->add('description', null, [
                'label' => $this->labelGroup . 'description',
            ])
            ->add('url', 'url', [
                'label' => $this->labelGroup . 'url',
            ])
            ->add('participantsOther', null, [
                'label' => $this->labelGroup . 'participants_other',
            ])
            ->add('serviceProviders', null, [
                'label' => $this->labelGroup . 'service_providers',
                'template' => 'General/service-providers.html.twig',
            ]);
    }
}
