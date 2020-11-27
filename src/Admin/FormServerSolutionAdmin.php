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

use App\Entity\Status;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class FormServerSolutionAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $hideFields = $this->getFormHideFields();
        if (!in_array('formServer', $hideFields, false)) {
            $formMapper
                ->add('formServer', ModelAutocompleteType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => FormServerAdmin::class
                ]);
        }
        if (!in_array('solution', $hideFields, false)) {
            $formMapper
                ->add('solution', ModelAutocompleteType::class, [
                    'property' => ['name', 'description'],
                    'required' => true,
                ], [
                    'admin_code' => SolutionAdmin::class
                ]);
        }
        $formMapper
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
                'choice_translation_domain' => false,
            ])
            ->add('articleNumber', TextType::class, [
                'required' => false,
            ])
            ->add('assistantType', TextType::class, [
                'required' => false,
            ])
            ->add('articleKey', TextType::class, [
                'required' => false,
            ])
            ->add('usableAsPrintTemplate', BooleanType::class, [
                'label' => 'app.form_server_solution.entity.usable_as_print_template',
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
                'translation_domain' => 'messages',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addDefaultDatagridFilter($datagridMapper, 'formServer');
        $this->addDefaultDatagridFilter($datagridMapper, 'solution');
        $datagridMapper->add('status');
        $datagridMapper->add('articleNumber');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('formServer', null, [
                'admin_code' => FormServerAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => SolutionAdmin::class
            ])
            ->add('articleNumber')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'status'],
                ]
            ]);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('formServer', null, [
                'admin_code' => FormServerAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => SolutionAdmin::class
            ])
            ->add('articleNumber')
            ->add('assistantType')
            ->add('articleKey')
            ->add('usableAsPrintTemplate')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ]);
    }
}
