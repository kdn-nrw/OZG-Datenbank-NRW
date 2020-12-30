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


use App\Admin\StateGroup\CommuneAdmin;
use App\Admin\StateGroup\ServiceProviderAdmin;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ModelRegionTrait;
use DateTime;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
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
    use ModelRegionTrait;
    use CommuneTrait;

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
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        parent::configureDatagridFilters($filter);
        $filter->add('communes',
            null, [
                'label' => 'app.user.entity.communes',
                'admin_code' => CommuneAdmin::class,
                'translation_domain' => 'messages',
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $filter->add('modelRegions',
            null, [
                'label' => 'app.user.entity.model_regions',
                'admin_code' => ModelRegionAdmin::class,
                'translation_domain' => 'messages',
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $filter->add('serviceProviders',
            null, [
                'label' => 'app.user.entity.service_providers',
                'admin_code' => ServiceProviderAdmin::class,
                'translation_domain' => 'messages',
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $filter->add('organisation',
            null, [
                'label' => 'app.user.entity.organisation',
                'admin_code' => OrganisationAdmin::class,
                'translation_domain' => 'messages',
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
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
        $showMapper
            ->with('Profile')
            ->add('organisation');
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
            ]);
        $formMapper
            ->end()
            ->end();
        $formMapper
            ->tab('User')
            ->with('app.user.groups.references', ['class' => 'col-md-6']);
        $formMapper->add('organisation', ModelType::class, [
            'label' => 'app.user.entity.organisation',
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'choice_translation_domain' => false,
        ]);
        $formMapper->add('communes', ModelType::class,
            [
                'label' => 'app.user.entity.communes',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
            ],
            [
                'admin_code' => CommuneAdmin::class,
            ]
        );
        $formMapper->add('modelRegions', ModelType::class, [
            'label' => 'app.user.entity.model_regions',
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ], [
            'admin_code' => ModelRegionAdmin::class,
        ]);
        $formMapper->add('serviceProviders', ModelType::class, [
            'label' => 'app.user.entity.service_providers',
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ], [
            'admin_code' => ServiceProviderAdmin::class,
        ]);
        $formMapper
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
