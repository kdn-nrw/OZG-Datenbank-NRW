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
use Sonata\AdminBundle\Form\Type\ModelType;

/**
 * Group admin
 */
class GroupAdmin extends \Sonata\UserBundle\Admin\Model\GroupAdmin
{

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        parent::configureDatagridFilters($filter);
        $filter->add('subGroups',
            null, [
                'label' => 'app.group.entity.sub_groups',
                'admin_code' => self::class,
                'translation_domain' => 'messages',
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        parent::configureListFields($listMapper);

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $listMapper->get('roles')->setTemplate('GroupAdmin/list__roles.html.twig');
        } else {
            $listMapper->remove('roles');
        }
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->tab('Group')
            ->with('General');
        $formMapper->add('subGroups', ModelType::class, [
            'label' => 'app.group.entity.sub_groups',
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
        $formMapper
            ->end()
            ->end();
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper->remove('roles');
            $formMapper->removeGroup('Roles', 'Security', true);
        }
    }

}
