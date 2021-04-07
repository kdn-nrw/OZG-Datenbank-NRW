<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Onboarding;


use App\Admin\AbstractAppAdmin;
use App\Admin\CustomFieldAdminInterface;
use App\Admin\StateGroup\CommuneAdmin;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\DependencyInjection\InjectionTraits\InjectSecurityTrait;
use App\Entity\Onboarding\AbstractOnboardingEntity;
use App\Entity\User;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\StringListFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

abstract class AbstractOnboardingAdmin extends AbstractAppAdmin implements CustomFieldAdminInterface
{
    use InjectManagerRegistryTrait;
    use InjectSecurityTrait;

    /**
     * Commune limits for the current user
     * @var bool|int[]
     */
    protected $currentUserCommuneLimits;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('commune', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ], [
                'admin_code' => CommuneAdmin::class,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $formMapper
            ->add('status', ChoiceType::class, [
                'label' => 'app.commune_info.entity.status',
                'choices' => array_flip(AbstractOnboardingEntity::$statusChoices),
                'required' => true,
                'expanded' => true,
                'choice_attr' => static function ($choice, $key, $value) {
                    return ['class' => 'onboarding-status ob-status-' . $value];
                },
            ]);
        $formMapper->end();
    }

    public function preUpdate($object)
    {
        /** @var AbstractOnboardingEntity $object */
        $object->calculateCompletionRate();
    }

    public function prePersist($object)
    {
        /** @var AbstractOnboardingEntity $object */
        $object->calculateCompletionRate();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addDefaultDatagridFilter($datagridMapper, 'commune');
        $datagridMapper
            ->add('status', StringListFilter::class, [], ChoiceType::class, [
                'label' => 'app.commune_info.entity.status',
                'choices' => array_flip(AbstractOnboardingEntity::$statusChoices),
                'multiple' => true,
                //'choice_translation_domain' => 'SonataAdminBundle',
            ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('commune', null, [
                'admin_code' => CommuneAdmin::class,
            ])
            ->add('modifiedAt')
            ->add('status', 'choice', [
                'label' => 'app.commune_info.entity.status',
                'template' => 'Onboarding/list-status.html.twig',
                'editable' => false,
                'choices' => AbstractOnboardingEntity::$statusChoices,
                //'catalogue' => 'SonataAdminBundle',
            ]);
        $securityHandler = $this->getSecurityHandler();
        if (null !== $securityHandler) {
            $extraActions = [
                'showQuestions' => [
                    'template' => 'Onboarding/Inquiry/action_show_inquiries.html.twig',
                    'route' => 'showQuestions',
                    //'permission' => sprintf($baseRole, 'LIST')
                ],
                'askQuestion' => [
                    'template' => 'General/CRUD/action_generic.html.twig',
                    'icon' => 'fa-question-circle-o',
                    'route' => 'askQuestion',
                    //'permission' => sprintf($baseRole, 'LIST')
                ],
            ];
        } else {
            $extraActions = null;
        }
        $this->addDefaultListActions($listMapper, $extraActions);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('commune', null, [
            'admin_code' => CommuneAdmin::class,
        ])
            ->add('modifiedAt')
            ->add('description')
            ->add('status', 'choice', [
                'label' => 'app.commune_info.entity.status',
                'editable' => false,
                'choices' => AbstractOnboardingEntity::$statusChoices,
                //'catalogue' => 'SonataAdminBundle',
            ])
            ->add('customValues');
    }

    /**
     * Returns the available commune ids for the current user; returns true if the user can see all communes
     *
     * @return bool|int[]
     */
    final public function getCurrentUserCommuneLimits()
    {
        if (null === $this->currentUserCommuneLimits) {
            $result = [0];
            $showAll = $this->isGranted('ALL');
            if (!$showAll) {
                if (null !== $this->security && null !== $user = $this->security->getUser()) {
                    /** @var User $user */
                    foreach ($user->getCommunes() as $commune) {
                        if (!$commune->isHidden()) {
                            $result[] = $commune->getId();
                        }
                    }
                }
            } else {
                $result = true;
            }
            $this->currentUserCommuneLimits = $result;
        }
        return $this->currentUserCommuneLimits;
    }

    /**
     * Exclude unassigned object
     *
     * @param int $id
     * @return AbstractOnboardingEntity|object|null
     */
    public function getObject($id)
    {
        $object = parent::getObject($id);
        if (null !== $object && true !== $communeLimits = $this->getCurrentUserCommuneLimits()) {
            /** @var AbstractOnboardingEntity|null $object */
            if ((null === $commune = $object->getCommune()) || !in_array($commune->getId(), $communeLimits, true)) {
                return null;
            }
        }

        return $object;
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $communeLimits = $this->getCurrentUserCommuneLimits();
        if (true !== $communeLimits) {
            /** @var \Doctrine\ORM\QueryBuilder $query */
            $query->andWhere(
                $query->getRootAliases()[0] . '.commune IN (:communeLimits)'
            );
            $query->setParameter('communeLimits', $communeLimits);
            $query->andWhere(
                $query->expr()->eq($query->getRootAliases()[0] . '.hidden', ':hidden')
            );
            $query->setParameter('hidden', 0);
        }
        return $query;
    }

    public function getAccessMapping()
    {
        if (!array_key_exists('askQuestion', $this->accessMapping)) {
            $this->accessMapping['askQuestion'] = 'ALL';
        }
        if (!array_key_exists('showQuestions', $this->accessMapping)) {
            $this->accessMapping['showQuestions'] = 'LIST';
        }
        return parent::getAccessMapping();
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->clearExcept(['list', 'edit']);
        $collection
            ->add('askQuestion', $this->getRouterIdParameter() . '/ask-question')
            ->add('showQuestions', $this->getRouterIdParameter() . '/show-questions');
    }

    public function hasRoute($name)
    {
        return in_array($name, ['list', 'edit', 'askQuestion', 'showQuestions'], false);
    }
}
