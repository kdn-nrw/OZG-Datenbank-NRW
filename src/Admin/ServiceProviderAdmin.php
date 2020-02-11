<?php

namespace App\Admin;

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\ContactTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class ServiceProviderAdmin extends AbstractAppAdmin
{
    use ContactTrait;
    use AddressTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('app.service_provider.group.general_data')
            ->with('general_data', [
                'label' => false,
                'box_class' => 'box-tab',
            ])
            ->add('name', TextType::class);
        $this->addAddressFormFields($formMapper);
        $formMapper->add('url', UrlType::class, [
                'required' => false
            ])
            ->add('communes', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
            ]);
        $formMapper->end();
        $formMapper->end();
        $this->addContactsFormFields($formMapper, true, true);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addAddressDatagridFilters($datagridMapper);
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
            ->add('url', 'url');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name');
        $this->addAddressShowFields($showMapper);
        $showMapper->add('url', 'url');
        $this->addContactsShowFields($showMapper, true);
        $showMapper->add('communes');
    }
}
