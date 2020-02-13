<?php

namespace App\Admin;

use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ManufaturerTrait;
use App\Admin\Traits\ServiceProviderTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SpecializedProcedureAdmin extends AbstractAppAdmin
{
    use CommuneTrait;
    use ManufaturerTrait;
    use ServiceProviderTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class);
        $this->addManufaturersFormFields($formMapper);
        $formMapper
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addServiceProvidersFormFields($formMapper);
        $this->addCommunesFormFields($formMapper);
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addManufaturersDatagridFilters($datagridMapper);
        $this->addCommunesDatagridFilters($datagridMapper);
        $this->addServiceProvidersDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name');
        $this->addManufaturersListFields($listMapper);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description');
        $this->addManufaturersShowFields($showMapper);
        $this->addServiceProvidersShowFields($showMapper);
        $this->addCommunesShowFields($showMapper);
    }
}
