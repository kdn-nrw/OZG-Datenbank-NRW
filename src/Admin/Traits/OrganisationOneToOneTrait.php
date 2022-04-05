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

namespace App\Admin\Traits;

use App\Admin\ContactAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Show\ShowMapper;

trait OrganisationOneToOneTrait
{
    protected function addOrganisationOneToOneFormFields(FormMapper $form, array $excludeFields = ['organizationType'])
    {
        $form->add('organisation', AdminType::class, [
            'label' => false,
            'delete' => false,
            'btn_add' => false,
            'btn_list' => false,
        ], [
            'ba_custom_exclude_fields' => $excludeFields,
        ]);
    }

    protected function addOrganisationOneToOneDatagridFilters(DatagridMapper $filter)
    {
        $this->addDefaultDatagridFilter($filter, 'organisation.contacts');
        $filter->add('organisation.zipCode');
        $filter->add('organisation.town');
    }

    protected function addOrganisationOneToOneListFields(ListMapper $list)
    {
        $list
            ->add('organisation.zipCode')
            ->add('organisation.town')
            ->add('organisation.url', 'url');
    }

    /**
     * @inheritdoc
     */
    public function addOrganisationOneToOneShowFields(ShowMapper $show)
    {
        $show
            ->add('organisation.street')
            ->add('organisation.zipCode')
            ->add('organisation.town')
            ->add('organisation.url', 'url')
            ->add('organisation.contacts', null, [
                'admin_code' => ContactAdmin::class,
            ]);
    }
}