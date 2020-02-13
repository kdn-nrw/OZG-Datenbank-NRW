<?php

namespace App\Admin\Traits;

use App\Admin\CommuneAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Trait CommuneTrait
 * @package App\Admin\Traits
 * @property array $customShowFields
 */
trait CommuneTrait
{
    protected function addCommunesFormFields(FormMapper $formMapper)
    {
        $formMapper->add('communes', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addCommunesDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('communes',
            null, [
                'admin_code' => CommuneAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addCommunesListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('communes', null,[
                'admin_code' => CommuneAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addCommunesShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('communes', null,[
                'admin_code' => CommuneAdmin::class,
            ]);
        $this->customShowFields[] = 'communes';
    }
}