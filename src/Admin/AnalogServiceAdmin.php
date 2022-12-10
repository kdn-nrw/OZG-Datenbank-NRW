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


use App\Admin\Traits\SolutionTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AnalogServiceAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use SolutionTrait;

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', TextType::class);
        $this->addSolutionsFormFields($form);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'solutions');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name');
        $this->addSolutionsListFields($list);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name');
        $this->addSolutionsShowFields($show);
    }
}
