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
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SituationAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class);
        if (!$this->isExcludedFormField('subject')) {
            $formMapper->add('subject', ModelListType::class, [
                'btn_add'       => 'app.common.model_list_type.add',       //Specify a custom label
                'btn_list'      => 'app.common.model_list_type.list',      //which will be translated
                'btn_delete'    => false,              //or hide the button.
                'btn_edit'      => 'app.common.model_list_type.edit',             //Hide add and show edit button when value is set
                'btn_catalogue' => 'messages', //Custom translation domain for buttons
            ], [
                'placeholder' => 'app.situation.entity.subject_placeholder',
            ]);
            /*$formMapper->add('subject', ModelType::class, [
                'btn_add' => false,
                'choice_translation_domain' => false,
            ]);*/
        }
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addDefaultDatagridFilter($datagridMapper, 'subject');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('subject');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('subject');
    }
}
