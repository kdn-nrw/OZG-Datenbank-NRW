<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;

use App\Admin\Traits\ImplementationProjectTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class FundingAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use ImplementationProjectTrait;

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addImplementationProjectsFormFields($form);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $filter->add('description');
        $this->addDefaultDatagridFilter($filter, 'implementationProjects');
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('name')
            ->add('description');

        $this->addImplementationProjectsShowFields($show);
    }
}
