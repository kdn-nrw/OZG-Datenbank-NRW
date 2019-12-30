<?php

namespace App\Admin;

use App\Admin\Traits\ServiceTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class LaboratoryAdmin extends AbstractAppAdmin
{
    use ServiceTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('url', UrlType::class, [
                'required' => false,
            ])
            ->add('serviceProviders', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
            ])
            ->add('participantsOther', TextareaType::class, [
                'required' => false,
            ]);
        $this->addServicesFormFields($formMapper);
        $formMapper->add('implementationUrl', UrlType::class, [
                'required' => false,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('serviceProviders',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $this->addServicesDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('url', 'url');
        $this->addServicesListFields($listMapper);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description')
            ->add('url', 'url')
            ->add('serviceProviders', null, [
                'template' => 'General/service-providers.html.twig',
            ])
            ->add('participantsOther');
        $this->addServicesShowFields($showMapper);
    }
}
