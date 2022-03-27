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
use App\Entity\MetaData\AbstractMetaItem;
use App\Entity\Onboarding\AbstractOnboardingEntity;
use App\Entity\User;
use App\Service\MetaData\InjectMetaDataManagerTrait;
use App\Service\Onboarding\InjectOnboardingManagerTrait;
use App\Util\SnakeCaseConverter;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Form\Type\Operator\NumberOperatorType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\NumberFilter;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

abstract class AbstractOnboardingAdmin extends AbstractAppAdmin implements CustomFieldAdminInterface
{
    use InjectMetaDataManagerTrait;
    use InjectManagerRegistryTrait;
    use InjectOnboardingManagerTrait;
    use InjectSecurityTrait;

    /**
     * Commune limits for the current user
     * @var bool|int[]
     */
    protected $currentUserCommuneLimits;

    protected function configureFormFields(FormMapper $form)
    {
        $form
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
        $this->addDataCompletenessConfirmedField($form);
        $form
            ->add('status', ChoiceType::class, [
                'label' => 'app.commune_info.entity.status',
                'choices' => array_flip(AbstractOnboardingEntity::$statusChoices),
                'required' => true,
                'expanded' => true,
                'choice_attr' => static function ($choice, $key, $value) {
                    return ['class' => 'onboarding-status ob-status-' . $value];
                },
            ]);
        $this->addGroupEmailFormField($form);
        $form->end();
    }

    /**
     * @param FormMapper $form
     */
    protected function addDataCompletenessConfirmedField(FormMapper $form)
    {
        $subject = $this->getSubject();
        if ($subject instanceof AbstractOnboardingEntity && $subject->getId() && !$subject->isDataCompletenessConfirmed()) {
            $form->add('dataCompletenessConfirmed', CheckboxType::class, [
                'required' => false,
            ]);
        }
    }

    public function getFormBuilder()
    {
        $formBuilder = parent::getFormBuilder();
        $metaItem = $this->metaDataManager->getObjectClassMetaData($this->getClass());
        if (null === $metaItem) {
            return $formBuilder;
        }

        $data = [
            AbstractMetaItem::META_TYPE_GROUP => $this->getFormGroups(),
            AbstractMetaItem::META_TYPE_TAB => $this->getFormTabs()
        ];
        foreach ($data as $metaType => &$metaTypeData) {
            if (empty($metaTypeData)) {
                continue;
            }
            foreach ($metaTypeData as $name => &$options) {
                $groupKey = SnakeCaseConverter::camelCaseToSnakeCase(str_replace('.', '_', $name));
                $metaKey = $metaType . '_' . $groupKey;
                $property = $metaItem->getMetaItemProperty($metaKey);
                if (null !== $property) {
                    $description = $property->getDescription();
                    if ($options['label'] !== false && $labelKey = $property->getLabelKey()) {
                        $options['label'] = $this->trans($labelKey);
                        $options['translation_domain'] = false;
                    }
                    if ($description) {
                        $options['description'] = $description;
                    }
                }
            }
            unset($options);
        }
        unset($metaTypeData);
        $this->setFormGroups($data[AbstractMetaItem::META_TYPE_GROUP]);
        $this->setFormTabs($data[AbstractMetaItem::META_TYPE_TAB]);

        return $formBuilder;
    }

    /**
     * Adds the group email form field (if access to field is granted)
     *
     * @param FormMapper $form
     * @param bool $useCustomLabel
     */
    protected function addGroupEmailFormField(FormMapper $form, $useCustomLabel = false): void
    {
        $form
            ->add('groupEmail', EmailType::class, [
                'label' => 'app.abstract_onboarding_entity.entity.group_email' . ($useCustomLabel ? '_custom' : ''),
                'required' => false,
            ]);
    }

    public function preUpdate($object)
    {
        parent::preUpdate($object);
        /** @var AbstractOnboardingEntity $object */
        $this->onboardingManager->beforeSave($object);
    }

    public function prePersist($object)
    {
        parent::prePersist($object);
        /** @var AbstractOnboardingEntity $object */
        $this->onboardingManager->beforeSave($object);
    }

    public function postPersist($object)
    {
        parent::postPersist($object);
        /** @var AbstractOnboardingEntity $object */
        $this->onboardingManager->afterSave($object);
    }

    /**
     * @param object $object
     */
    public function postUpdate($object)
    {
        parent::postUpdate($object);
        // Notifications are sent in the parent postUpdate function; only change status after notification
        // for completion has been sent
        if ($object instanceof AbstractOnboardingEntity
            && $object->isDataCompletenessConfirmed()
            && $object->getStatus() < AbstractOnboardingEntity::STATUS_COMPLETE_CONFIRMED) {
            $object->setStatus(AbstractOnboardingEntity::STATUS_COMPLETE_CONFIRMED);
            $this->getModelManager()->update($object);
        }
        /** @var AbstractOnboardingEntity $object */
        $this->onboardingManager->afterSave($object);
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $this->addDefaultDatagridFilter($filter, 'commune');
        $filter->add('status',
            CallbackFilter::class, [
                'label' => 'app.commune_info.entity.status',
                'callback' => function (ProxyQueryInterface $queryBuilder, $alias, $field, $value) {
                    if (!is_array($value) || !\array_key_exists('value', $value)) {
                        return false;
                    }

                    if (!\is_array($value['value'])) {
                        $value['value'] = [$value['value']];
                    }
                    $type = $value['type'] ?? NumberOperatorType::TYPE_EQUAL;
                    $operator = NumberFilter::CHOICES[$type] ?? NumberFilter::CHOICES[NumberOperatorType::TYPE_EQUAL];
                    $andConditions = $queryBuilder->expr()->andX();
                    foreach ($value['value'] as $item) {
                        $andConditions->add(sprintf('%s.%s %s %d', $alias, $field, $operator, (int)$item));
                    }
                    $queryBuilder->andWhere($andConditions);
                    return true;
                },
            ],
            ChoiceType::class,
            [
                'label' => 'app.commune_info.entity.status',
                'choices' => array_flip(AbstractOnboardingEntity::$statusChoices),
                'multiple' => true,
                'expanded' => false,
            ]
        );
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('commune', null, [
                'admin_code' => CommuneAdmin::class,
            ])
            ->add('modifiedAt');
        $this->addListStatusField($list);
        $this->addOnboardingDefaultListActions($list);
    }

    /**
     * Adds the list status field
     *
     * @param ListMapper $list
     */
    protected function addListStatusField(ListMapper $list): void
    {
        $list
            ->add('status', TemplateRegistryInterface::TYPE_CHOICE, [
                'label' => 'app.commune_info.entity.status',
                'template' => 'Onboarding/list-status.html.twig',
                'editable' => false,
                'choices' => AbstractOnboardingEntity::$statusChoices,
                //'catalogue' => 'SonataAdminBundle',
            ]);
    }

    /**
     * Adds the default list actions to the list mapper
     *
     * @param ListMapper $list
     * @param array|null $extraActions
     */
    protected function addOnboardingDefaultListActions(ListMapper $list, ?array $extraActions = null): void
    {
        $securityHandler = $this->getSecurityHandler();
        if (null !== $securityHandler) {
            if (null === $extraActions) {
                $extraActions = [];
            }
            $extraActions = array_merge([
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
            ], $extraActions);
        }
        $this->addDefaultListActions($list, $extraActions);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show->add('commune', null, [
            'admin_code' => CommuneAdmin::class,
        ])
            ->add('modifiedAt')
            ->add('description')
            ->add('status', TemplateRegistryInterface::TYPE_CHOICE, [
                'label' => 'app.commune_info.entity.status',
                'editable' => false,
                'choices' => AbstractOnboardingEntity::$statusChoices,
                'catalogue' => 'messages',
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
        $collection->clearExcept(['list', 'edit', 'history', 'history_view_revision', 'history_compare_revisions']);
        $collection
            ->add('askQuestion', $this->getRouterIdParameter() . '/ask-question')
            ->add('showQuestions', $this->getRouterIdParameter() . '/show-questions');
    }

    public function hasRoute($name)
    {
        if (in_array($name, ['create', 'delete', 'export'])) {
            return false;
        }
        //['list', 'edit', 'download', 'askQuestion', 'showQuestions', 'history']
        return parent::hasRoute($name);
    }
}
