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


use App\Admin\ModelRegion\ModelRegionAdmin;
use App\Admin\StateGroup\CommuneAdmin;
use App\Admin\StateGroup\ServiceProviderAdmin;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ModelRegionTrait;
use DateTime;
use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User admin
 */
class UserAdmin extends AbstractAdmin
{
    use AdminTranslatorStrategyTrait;
    use ModelRegionTrait;
    use CommuneTrait;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        $options['validation_groups'] = ['Default', 'Profile'];

        if (!$this->getSubject() || null === $this->getSubject()->getId()) {
            $options['validation_groups'] = ['Default', 'Registration'];
        }

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user): void
    {
        if ($user instanceof UserInterface) {
            $this->getUserManager()->updateCanonicalFields($user);
            $this->getUserManager()->updatePassword($user);
        }
    }

    public function setUserManager(UserManagerInterface $userManager): void
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

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
        $filter
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('groups');
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


    /**
     * {@inheritdoc}
     */
    protected function configureDefaultUserFormFields(FormMapper $formMapper): void
    {
        // define group zoning
        $formMapper
            ->tab('User')
            ->with('Profile', ['class' => 'col-md-6'])->end()
            ->with('General', ['class' => 'col-md-6'])->end()
            //->with('Social', ['class' => 'col-md-6'])->end()
            ->end();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {

            $formMapper
                ->tab('Security')
                ->with('Status', ['class' => 'col-md-4'])->end()
                ->with('Groups', ['class' => 'col-md-4'])->end()
                //->with('Keys', ['class' => 'col-md-4'])->end()
                ->with('Roles', ['class' => 'col-md-12'])->end()
                ->end();
        }

        $now = new \DateTime();

        $genderOptions = [
            'choices' => \call_user_func([$this->getUserManager()->getClass(), 'getGenderList']),
            'required' => true,
            'translation_domain' => $this->getTranslationDomain(),
        ];

        $formMapper
            ->tab('User')
            ->with('General')
            ->add('username')
            ->add('email')
            ->add('plainPassword', TextType::class, [
                'required' => (!$this->getSubject() || null === $this->getSubject()->getId()),
            ])
            ->end()
            ->with('Profile')
            /*->add('dateOfBirth', DatePickerType::class, [
                'years' => range(1900, $now->format('Y')),
                'dp_min_date' => '1-1-1900',
                'dp_max_date' => $now->format('c'),
                'required' => false,
            ])*/
            ->add('firstname', null, ['required' => false])
            ->add('lastname', null, ['required' => false])
            ->add('website', UrlType::class, ['required' => false])
            ->add('gender', ChoiceType::class, $genderOptions)
            ->add('locale', LocaleType::class, ['required' => false])
            ->add('timezone', TimezoneType::class, ['required' => false])
            ->add('phone', null, ['required' => false])
            ->end()
            /*
                ->with('Social')
                    ->add('facebookUid', null, ['required' => false])
                    ->add('facebookName', null, ['required' => false])
                    ->add('twitterUid', null, ['required' => false])
                    ->add('twitterName', null, ['required' => false])
                    ->add('gplusUid', null, ['required' => false])
                    ->add('gplusName', null, ['required' => false])
                ->end()*/
            ->end();
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Security')
                ->with('Status')
                ->add('enabled', null, ['required' => false])
                ->end()
                ->with('Groups')
                ->add('groups', ModelType::class, [
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ])
                ->end()
                /*->with('Roles')
                ->add('realRoles', SecurityRolesType::class, [
                    'label' => 'form.label_roles',
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                ])
                ->end()*/
                /*->with('Keys')
                ->add('token', null, ['required' => false])
                ->add('twoStepVerificationCode', null, ['required' => false])
                ->end()*/
                ->end();
        }
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $this->configureDefaultUserFormFields($formMapper);

//        $formMapper->remove('timezone');
//        $formMapper->remove('website');
//        $formMapper->remove('phone');

        //$formMapper->removeGroup('Roles', 'Security');
        //$formMapper->removeGroup('Keys', 'Security');
        $now = new DateTime();
        $formMapper
            ->tab('User')
            ->with('Profile')
            ->add('website', UrlType::class, ['required' => false])
            ->add('timezone', TimezoneType::class, ['required' => false])
            ->add('phone', null, ['required' => false])/*->add('dateOfBirth', DatePickerType::class, [
                'years' => range(1900, $now->format('Y')),
                'dp_min_date' => '1-1-1900',
                'dp_max_date' => $now->format('c'),
                'required' => false,
            ])*/
        ;
        $formMapper
            ->end()
            ->end();
        if ($this->isGranted('ALL')) {
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
            $formMapper->add('serviceProviders', ModelAutocompleteType::class, [
                'label' => 'app.user.entity.service_providers',
                'property' => ['name', 'shortName'],
                'required' => false,
                'multiple' => true,
            ], [
                'admin_code' => ServiceProviderAdmin::class,
            ]);/*
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
                ]);*/
            $formMapper
                ->end()
                ->end();
        }
    }

    protected function configureExportFields(): array
    {
        // Avoid sensitive properties to be exported.
        return array_filter(parent::configureExportFields(), static function (string $v): bool {
            return !\in_array($v, ['password', 'salt'], true);
        });
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->with('General')
            ->add('username')
            ->add('email')
            ->end()
            ->with('Groups')
            ->add('groups')
            ->end()
            ->with('Profile')
            ->add('firstname')
            ->add('lastname')
            ->add('organisation', null, [
                'label' => 'app.user.entity.organisation',
            ])
            ->add('website')
            ->add('biography')
            ->add('gender')
            ->add('locale')
            ->add('timezone')
            ->add('phone')
            //->add('dateOfBirth')
            ->end()
            ->with('Security')
            ->add('token')
            ->add('twoStepVerificationCode')
            ->end();
    }

}
