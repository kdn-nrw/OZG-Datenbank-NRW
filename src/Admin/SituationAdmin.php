<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SituationAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.situation.';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, ['label' => $this->labelGroup . 'name'])
            ->add('subject', ModelType::class, [
                'btn_add' => false,
                'label' => $this->labelGroup . 'subject',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name',
            null,
            ['label' => $this->labelGroup . 'name']
        );
        $datagridMapper->add('subject',
            null,
            ['label' => $this->labelGroup . 'subject'],
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
            ->add('subject', null, [
                'label' => $this->labelGroup . 'subject',
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
            ->add('subject', null, [
                'label' => $this->labelGroup . 'subject',
            ]);
    }
}
