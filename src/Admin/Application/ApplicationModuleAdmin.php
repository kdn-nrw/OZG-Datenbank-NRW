<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Application;

use App\Admin\AbstractAppAdmin;
use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\SpecializedProcedureAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ApplicationModuleAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected $baseRoutePattern = 'application/module';

    protected function configureFormFields(FormMapper $form)
    {
        if (!$this->isExcludedFormField('application')) {
            $form
                ->add('application', ModelType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => SpecializedProcedureAdmin::class
                ]);
        }
        $form
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);

        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        parent::configureDatagridFilters($filter);
        $this->addDefaultDatagridFilter($filter, 'application');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name');
        $list
            ->add('application', null, [
                'admin_code' => SpecializedProcedureAdmin::class
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('application')
            ->add('name')
            ->add('description');
    }
}
