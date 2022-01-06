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
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        parent::configureListFields($list);

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $list->get('roles')->setTemplate('GroupAdmin/list__roles.html.twig');
        } else {
            $list->remove('roles');
        }
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        parent::configureFormFields($form);

        $form
            ->tab('Group')
            ->with('General');
        $form->add('subGroups', ModelType::class, [
            'label' => 'app.group.entity.sub_groups',
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
        $form
            ->end()
            ->end();
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $form->remove('roles');
            $form->removeGroup('Roles', 'Security', true);
        }
    }

}
