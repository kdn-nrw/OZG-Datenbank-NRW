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
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class PaymentTypeAdmin extends AbstractAppAdmin
{
    use SolutionTrait;

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name', TextType::class)
            ->add('url', UrlType::class, [
                'required' => false
            ]);
        $this->addSolutionsFormFields($form);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'solutions');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name');
        $this->addSolutionsListFields($list);
        $list->add('url', 'url');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('name')
            ->add('url', 'url');
        $this->addSolutionsShowFields($show);
    }
}
