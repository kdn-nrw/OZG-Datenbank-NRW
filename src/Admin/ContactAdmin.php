<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class ContactAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', TextType::class)
            ->add('street', TextType::class, [
                'required' => false,
            ])
            ->add('zipcode', TextType::class, [
                'required' => false,
            ])
            ->add('town', TextType::class, [
                'required' => false,
            ])
            ->add('organisation', TextType::class, [
                'required' => false,
            ])
            ->add('position', TextType::class, [
                'required' => false,
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('lastName');
        $datagridMapper->add('organisation');
        $datagridMapper->add('town');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('lastName')
            ->add('firstName')
            ->add('email')
            ->add('organisation')
            ->add('town')
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
            ->add('lastName')
            ->add('firstName')
            ->add('email')
            ->add('street')
            ->add('zipcode')
            ->add('town')
            ->add('organisation')
            ->add('position')
            ->add('phoneNumber');
    }
}
