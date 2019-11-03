<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class ServiceProviderAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('street', TextType::class, [
                'required' => false
            ])
            ->add('zipCode', TextType::class, [
                'required' => false
            ])
            ->add('town', TextType::class, [
                'required' => false
            ])
            ->add('url', UrlType::class, [
                'required' => false
            ])
            ->add('contact', TextareaType::class, [
                'required' => false
            ])
            ->add('communes', ModelType::class, [
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
        $datagridMapper->add('name');
        $datagridMapper->add('zipCode');
        $datagridMapper->add('town');
        $datagridMapper->add('communes',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('url', 'url', [
            ])
            ->add('_action', null, [
                'label' => 'app.common.actions',
                'translation_domain' => 'messages',
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
            ->add('name')
            ->add('street')
            ->add('zipCode')
            ->add('town')
            ->add('url', 'url', [
            ])
            ->add('contact')
            ->add('communes', null, [
                'template' => 'ServiceProviderAdmin/communes.html.twig',
            ]);
    }
}
