<?php

namespace App\Admin;

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class ServiceProviderAdmin extends AbstractAppAdmin
{
    use CommuneTrait;
    use ContactTrait;
    use AddressTrait;
    use SpecializedProcedureTrait;

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
            ]);
        $this->addCommunesFormFields($formMapper);
        $this->addSpecializedProceduresFormFields($formMapper);
        $formMapper->end();
        $formMapper->end();
        $this->addContactsFormFields($formMapper, true, true);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addAddressDatagridFilters($datagridMapper);
        $this->addCommunesDatagridFilters($datagridMapper);
        $this->addSpecializedProceduresDatagridFilters($datagridMapper);
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
        $this->addCommunesShowFields($showMapper);
        $this->addSpecializedProceduresShowFields($showMapper);
        $showMapper->add('specializedProcedures.manufacturers', null, [
            'label' => 'app.specialized_procedure.entity.manufacturers',
            'admin_code' => ManufacturerAdmin::class,
            'template' => 'CommuneAdmin/show-specialized-procedures-manufacturers.html.twig',
        ]);
    }
}
