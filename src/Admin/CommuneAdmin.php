<?php

namespace App\Admin;

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\ServiceProviderTrait;
use App\Admin\Traits\SpecializedProcedureTrait;
use App\Entity\Commune;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class CommuneAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use AddressTrait;
    use ContactTrait;
    use ServiceProviderTrait;
    use SpecializedProcedureTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('app.commune.group.general_data')
            ->with('app.commune.group.general_data', [
                'label' => false,
                'box_class' => 'box-tab',
            ])
                ->add('name', TextType::class);
        $this->addAddressFormFields($formMapper);
        $formMapper->add('url', UrlType::class, [
                    'required' => false,
                ]);
        $this->addServiceProvidersFormFields($formMapper);
        $formMapper->end();
        $formMapper->end();
        $this->addContactsFormFields($formMapper, true, true);
        $this->addSpecializedProceduresFormFields($formMapper);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addAddressDatagridFilters($datagridMapper);
        $this->addContactsDatagridFilters($datagridMapper);
        $this->addServiceProvidersDatagridFilters($datagridMapper);
        $this->addSpecializedProceduresDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $this->addAddressListFields($listMapper);
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
        $this->addServiceProvidersShowFields($showMapper);
        $this->addSpecializedProceduresShowFields($showMapper);
    }

    public function toString($object)
    {
        return $object instanceof Commune
            ? $object->getName()
            : 'Commune'; // shown in the breadcrumb on the create view
    }
}
