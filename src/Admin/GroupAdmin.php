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
use App\Sonata\UserBundle\Form\Type\SecurityRolesType;

/**
 * Group admin
 */
class GroupAdmin extends AbstractAppAdmin
{
    protected $formOptions = [
        'validation_groups' => 'Registration',
    ];

    protected function createNewInstance(): object
    {
        $class = $this->getClass();
        $object = new $class('', []);
        $this->appendParentObject($object);

        return $object;
    }

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
            ['expanded' => false, 'multiple' => true]
        );
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        parent::configureListFields($list);
        $list
            ->add('roles');

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
        $form
            ->tab('Group')
            ->with('General', ['class' => 'col-md-6'])
            ->add('name')
            ->end()
            ->end()
            ->tab('Security')
            ->with('Roles', ['class' => 'col-md-12'])
            ->add('roles', SecurityRolesType::class, [
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->end()
            ->end();

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
