<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SubjectAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('situations', CollectionType::class, [
                'type_options' => [
                    'delete' => true,
                ],
                'by_reference' => false,
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
                'ba_custom_hide_fields' => ['subject'],
            ])
            ->add('contact', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('situations',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('contact');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('contact')
            ->addIdentifier('situations');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('contact');
    }
}
