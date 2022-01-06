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


use App\Admin\Traits\IsExcludedFormField;
use App\DependencyInjection\InjectionTraits\InjectEventDispatcherTrait;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Base\CustomEntityLabelInterface;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\SortableEntityInterface;
use App\EventSubscriber\EntityPostCreateEvent;
use App\EventSubscriber\EntityPostUpdateEvent;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AbstractAppAdmin
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
abstract class AbstractAppAdmin extends AbstractContextAwareAdmin
{
    use AdminTranslatorStrategyTrait;
    use IsExcludedFormField;
    use InjectEventDispatcherTrait;

    /**
     * Label for the default show group header
     * @var string
     */
    protected $defaultShowGroupLabel = 'object_name';

    /**
     * @var bool
     */
    private $sortableBehaviourEnabled = false;

    /**
     * Returns the default fields for the given action; used to auto-configure CRUD display for simple admins
     *
     * @param string $adminAction The executed admin action (form, list, show or filter)
     * @return array Field array with field configuration
     */
    protected function getDefaultFields($adminAction): array
    {
        $defaultFields = [];
        if (is_subclass_of($this->getClass(), NamedEntityInterface::class)) {
            $defaultFields['name'] = [
                'type' => $adminAction === 'form' ? TextType::class : null,
                'options' => [],
                'fieldDescriptionOptions' => [],
            ];
        }
        return $defaultFields;
    }

    /**
     * This method is called by autowiring
     */
    public function enableSortableBehavior()
    {
        // Install pixassociates/sortable-behavior-bundle to enable this feature
        if (is_subclass_of($this->getClass(), SortableEntityInterface::class, true)
            && method_exists($this->getBaseControllerName(), 'moveAction')) {
            $this->sortableBehaviourEnabled = true;
        }
    }

    /**
     * Configures a list of default sort values.
     *
     * @phpstan-param array{_page?: int, _per_page?: int, _sort_by?: string, _sort_order?: string} $sortValues
     * @param array $sortValues
     */
    protected function configureDefaultSortValues(array &$sortValues)
    {
        if ($this->sortableBehaviourEnabled = true) {
            $sortValues['_sort_order'] = $sortValues['_sort_order'] ?? 'ASC';
            $sortValues['_sort_by'] = $sortValues['_sort_by'] ?? 'sorting';
        }
    }

    /**
     * @return bool
     */
    public function isSortableBehaviourEnabled(): bool
    {
        return $this->sortableBehaviourEnabled;
    }


    protected function configureFormFields(FormMapper $form)
    {
        $defaultFields = $this->getDefaultFields('form');
        foreach ($defaultFields as $field => $fieldConfig) {
            $options = $fieldConfig['options'] ?? [];
            $fieldDescriptionOptions = $fieldConfig['fieldDescriptionOptions'] ?? [];
            $form->add($field, $fieldConfig['type'], $options, $fieldDescriptionOptions);
        }
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $defaultFields = $this->getDefaultFields('filter');
        foreach ($defaultFields as $field => $fieldConfig) {
            $filterOptions = $fieldConfig['filterOptions'] ?? [];
            $filter->add($field, $fieldConfig['type'], $filterOptions);
        }
    }

    protected function addDefaultListFields(ListMapper $list): void
    {
        $defaultFields = $this->getDefaultFields('list');
        foreach ($defaultFields as $field => $fieldConfig) {
            $fieldDescriptionOptions = $fieldConfig['fieldDescriptionOptions'] ?? [];
            $list->add($field, $fieldConfig['type'], $fieldDescriptionOptions);
        }
    }

    protected function configureListFields(ListMapper $list)
    {
        $this->addDefaultListFields($list);
        $this->addDefaultListActions($list);
    }

    /**
     * Adds the default list actions to the list mapper
     *
     * @param ListMapper $list
     * @param array|null $extraActions
     */
    protected function addDefaultListActions(ListMapper $list, ?array $extraActions = null): void
    {
        $actions = [
            'show' => [],
            'edit' => [],
            'delete' => [],
        ];
        if ($extraActions) {
            $actions = array_merge($actions, $extraActions);
        }
        // Install pixassociates/sortable-behavior-bundle to enable this feature
        /*'if ($this->isSortableBehaviourEnabled()) {
            $actions['move'] = ['template' => '@PixSortableBehavior/Default/_sort.html.twig'];
        }*/
        $list
            ->add('_action', null, [
                'label' => 'app.common.actions',
                'translation_domain' => 'messages',
                'actions' => $actions,
            ])
        ;
    }

    /**
     * Override the label for the default group by calling this function before any fields have been added
     * The label should be overridden, of the
     *
     * @param ShowMapper $show
     */
    final protected function setDefaultShowGroupLabel(ShowMapper $show): void
    {
        $groups = $this->getShowGroups();
        if (!$groups) {
            $translatorStrategy = $this->getLabelTranslatorStrategy();
            $show->with('default', [
                'auto_created' => true,
                'label' => $translatorStrategy->getLabel($this->defaultShowGroupLabel)
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $this->setDefaultShowGroupLabel($show);
        $defaultFields = $this->getDefaultFields('show');
        foreach ($defaultFields as $field => $fieldConfig) {
            $fieldDescriptionOptions = $fieldConfig['fieldDescriptionOptions'] ?? [];
            $show->add($field, $fieldConfig['type'], $fieldDescriptionOptions);
        }
    }

    public function toString($object)
    {
        if ($object instanceof CustomEntityLabelInterface) {
            return $this->trans($object->getLabelKey());
        }
        return parent::toString($object);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        if ($this->isSortableBehaviourEnabled()) {
            $collection
                ->add('move', $this->getRouterIdParameter().'/move/{position}')
            ;
        }
    }

    public function getAccessMapping()
    {
        if (!array_key_exists('move', $this->accessMapping)
            && $this->isSortableBehaviourEnabled() ) {
            $this->accessMapping['move'] = 'EDIT';
        }
        return parent::getAccessMapping();
    }

    /**
     * @param object $object
     */
    public function postUpdate($object)
    {
        if (null !== $this->eventDispatcher && $object instanceof BaseEntityInterface) {
            $postUpdateEvent = new EntityPostUpdateEvent($this, $object);
            $this->eventDispatcher->dispatch($postUpdateEvent);
        }
    }

    /**
     * @param object $object
     */
    public function postPersist($object)
    {
        if (null !== $this->eventDispatcher && $object instanceof BaseEntityInterface) {
            $postUpdateEvent = new EntityPostCreateEvent($this, $object);
            $this->eventDispatcher->dispatch($postUpdateEvent);
        }
    }
}
