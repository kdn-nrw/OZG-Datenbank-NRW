<?php

namespace App\Admin\Traits;

use App\Admin\OrganisationAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;

trait OrganisationTrait
{
    protected function addOrganisationsFormFields(FormMapper $formMapper, $fieldName = 'organisations')
    {
        $formMapper->add($fieldName, ModelAutocompleteType::class, [
            'btn_add' => false,//'app.common.model_list_type.add',
            'property' => 'name',
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'btn_catalogue' => 'messages',
        ], [
                'admin_code' => OrganisationAdmin::class,
            ]
        );
    }

    protected function addOrganisationsDatagridFilters(DatagridMapper $datagridMapper, $fieldName = 'organisations')
    {
        $datagridMapper->add($fieldName,
            null, [
                'admin_code' => OrganisationAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addOrganisationsListFields(ListMapper $listMapper, $fieldName = 'organisations')
    {
        $listMapper
            ->add($fieldName, null,[
                'admin_code' => OrganisationAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addOrganisationsShowFields(ShowMapper $showMapper, $fieldName = 'organisations')
    {
        $showMapper
            ->add($fieldName, null,[
                'admin_code' => OrganisationAdmin::class,
            ]);
    }
}