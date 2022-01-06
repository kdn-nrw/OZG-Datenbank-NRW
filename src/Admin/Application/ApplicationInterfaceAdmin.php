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
use App\Admin\ApplicationAdmin;
use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\Traits\SpecializedProcedureTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ApplicationInterfaceAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected $baseRoutePattern = 'application/interface';

    use SpecializedProcedureTrait;

    protected function configureFormFields(FormMapper $form)
    {
        if (!$this->isExcludedFormField('application')) {
            $form
                ->add('application', ModelType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => ApplicationAdmin::class
                ]);
        }
        $form
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addSpecializedProceduresFormFields($form);

        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        parent::configureDatagridFilters($filter);
        $this->addDefaultDatagridFilter($filter, 'application');
        $this->addDefaultDatagridFilter($filter, 'specializedProcedures');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name');
        $list
            ->add('application', null, [
                'admin_code' => ApplicationAdmin::class
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
        $this->addSpecializedProceduresShowFields($show);
    }
}
