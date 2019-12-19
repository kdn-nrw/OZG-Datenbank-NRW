<?php

namespace App\Admin;

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\ServiceSystemTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class MinistryStateAdmin extends AbstractAppAdmin
{
    use ContactTrait;
    use AddressTrait;
    use ServiceSystemTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('app.ministry_state.tabs.default')
                ->with('general', [
                    'label' => false,
                ])
                ->add('name', TextType::class)
                ->add('shortName', TextType::class);
        $this->addAddressFormFields($formMapper);
        $formMapper->add('url', UrlType::class, [
            'required' => false,
        ]);
        $this->addServiceSystemsFormFields($formMapper);
        $formMapper->end();
        $formMapper->end();
        $this->addContactsFormFields($formMapper, false, true);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addAddressDatagridFilters($datagridMapper);
        $this->addContactsDatagridFilters($datagridMapper);
        $this->addServiceSystemsDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $this->addAddressListFields($listMapper);
        $listMapper->add('url', 'url');
        //$this->addServiceSystemsListFields($listMapper);
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
        $this->addServiceSystemsShowFields($showMapper);
        $this->addContactsShowFields($showMapper);
    }
}
