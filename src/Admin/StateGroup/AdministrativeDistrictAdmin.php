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

namespace App\Admin\StateGroup;


use App\Admin\AbstractAppAdmin;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ServiceProviderTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdministrativeDistrictAdmin extends AbstractAppAdmin
{
    use ServiceProviderTrait;

    protected $baseRoutePattern = 'state/administrative-district';

    use CommuneTrait;

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('name', TextType::class);
        $this->addCommunesFormFields($form);
        $form
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addServiceProvidersFormFields($form, 'paymentOperator', 'paymentProvider');
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'communes');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name');
        $this->addCommunesListFields($list);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name')
            ->add('description');
        $this->addCommunesShowFields($show);
    }
}
