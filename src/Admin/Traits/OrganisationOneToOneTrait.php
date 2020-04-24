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
    protected function addOrganisationOneToOneFormFields(FormMapper $formMapper)
    {
        $formMapper->add('organisation', AdminType::class, [
            'label' => false,
            'delete' => false,
            'btn_add' => false,
            'btn_list' => false,
        ], [
            'ba_custom_hide_fields' => ['organizationType'],
        ]);
    }

    protected function addOrganisationOneToOneDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('organisation.contacts',
            null, [
                'admin_code' => ContactAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('organisation.zipCode');
        $datagridMapper->add('organisation.town');
    }

    protected function addOrganisationOneToOneListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('organisation.zipCode')
            ->add('organisation.town')
            ->add('organisation.url', 'url');
    }

    /**
     * @inheritdoc
     */
    public function addOrganisationOneToOneShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('organisation.street')
            ->add('organisation.zipCode')
            ->add('organisation.town')
            ->add('organisation.url', 'url')
            ->add('organisation.contacts', null, [
                'admin_code' => ContactAdmin::class,
            ]);
    }
}