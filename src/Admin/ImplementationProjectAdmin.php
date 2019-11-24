<?php

namespace App\Admin;

use App\Entity\ImplementationStatus;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ImplementationProjectAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('solutions', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('serviceSystems', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('solutions',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSystems',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSystems.situation.subject',
            null,
            ['label' => 'app.situation.entity.subject'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('solutions')
            ->add('serviceSystems')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
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
            ->add('solutions')
            ->add('serviceSystems')
            ->add('description')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
    }
}
