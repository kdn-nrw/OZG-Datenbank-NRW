<?php

namespace App\Admin;


use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Group admin
 */
class GroupAdmin extends \Sonata\UserBundle\Admin\Model\GroupAdmin
{
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

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper->remove('roles');
            $formMapper->removeGroup('Roles', 'Security', true);
        }
    }


}
