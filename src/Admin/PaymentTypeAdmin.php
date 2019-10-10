<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class PaymentTypeAdmin extends AbstractAdmin
{
    protected $labelGroup = 'app.entity.payment_type.';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, ['label' => $this->labelGroup . 'name'])
            ->add('url', UrlType::class, [
                'required' => false,
                'label' => $this->labelGroup . 'url'
            ])
            ->add('solutions', ModelType::class, [
                'label' => $this->labelGroup . 'solutions',
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
        $datagridMapper->add('solutions',
            null,
            ['label' => $this->labelGroup . 'solutions'],
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
            ->add('solutions', null, [
                'label' => $this->labelGroup . 'solutions',
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
            ->add('url', 'url', [
                'label' => $this->labelGroup . 'url',
            ])
            ->add('solutions', null, [
                'label' => $this->labelGroup . 'solutions',
            ]);
    }
}
