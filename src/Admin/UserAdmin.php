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


use DateTime;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * User admin
 */
class UserAdmin extends \Sonata\UserBundle\Admin\Model\UserAdmin
{
    use AdminTranslatorStrategyTrait;

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $query->andWhere(
                $query->expr()->notLike($query->getRootAliases()[0] . '.roles', ':superAdminRole')
            );
            $query->setParameter('superAdminRole', '%"ROLE_SUPER_ADMIN"%');
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            //->addIdentifier('username')
            ->addIdentifier('email')
            ->add('firstname', null, [
                'label' => 'app.user.entity.firstname',
                'translation_domain' => 'messages',
            ])
            ->add('lastname', null, [
                'label' => 'app.user.entity.lastname',
                'translation_domain' => 'messages',
            ]);
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $listMapper
                ->add('groups');
        }
        $listMapper
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt');
        $listMapper->add('_action', null, [
            'label' => 'app.common.actions',
            'translation_domain' => 'messages',
            'actions' => [
                //'show' => [],
                'edit' => [],
                //z'delete' => [],
            ]
        ]);

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', ['template' => '@SonataUser/Admin/Field/impersonating.html.twig']);
        }
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        parent::configureShowFields($showMapper);

        $showMapper->removeGroup('Social');
        $showMapper->remove('biography');
//        $showMapper->remove('timezone');
//        $showMapper->remove('website');
//        $showMapper->remove('phone');
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);

        $formMapper->removeGroup('Social', 'User');
        $formMapper->remove('biography');
//        $formMapper->remove('timezone');
//        $formMapper->remove('website');
//        $formMapper->remove('phone');

        $formMapper->removeGroup('Roles', 'Security');
        $formMapper->removeGroup('Keys', 'Security');
        $formMapper->remove('dateOfBirth');
        $now = new DateTime();
        $formMapper
            ->tab('User')
            ->with('Profile')
            ->add('website', UrlType::class, ['required' => false])
            //->add('biography', TextType::class, ['required' => false])
            ->add('timezone', TimezoneType::class, ['required' => false])
            ->add('phone', null, ['required' => false])
            ->add('dateOfBirth', DatePickerType::class, [
                'years' => range(1900, $now->format('Y')),
                'dp_min_date' => '1-1-1900',
                'dp_max_date' => $now->format('c'),
                'required' => false,
            ])
            ->end()
            ->end();
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper->remove('enabled');
            $formMapper->remove('realRoles');
            $formMapper->remove('groups');
            $formMapper->removeGroup('Groups', 'Security', true);
            $formMapper->removeGroup('Status', 'Security', true);
        }
    }

}
