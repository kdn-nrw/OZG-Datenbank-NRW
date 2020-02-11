<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */

namespace App\Admin;


use App\Entity\Base\NamedEntityInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AbstractAppAdmin
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
abstract class AbstractAppAdmin extends AbstractAdmin
{
    use AdminTranslatorStrategyTrait;

    /**
     * Label for the default show group header
     * @var string
     */
    protected $defaultShowGroupLabel = 'object_name';

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


    protected function configureFormFields(FormMapper $formMapper)
    {
        $defaultFields = $this->getDefaultFields('form');
        foreach ($defaultFields as $field => $fieldConfig) {
            $options = $fieldConfig['options'] ?? [];
            $fieldDescriptionOptions = $fieldConfig['fieldDescriptionOptions'] ?? [];
            $formMapper->add($field, $fieldConfig['type'], $options, $fieldDescriptionOptions);
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $defaultFields = $this->getDefaultFields('filter');
        foreach ($defaultFields as $field => $fieldConfig) {
            $filterOptions = $fieldConfig['filterOptions'] ?? [];
            $datagridMapper->add($field, $fieldConfig['type'], $filterOptions);
        }
    }

    protected function addDefaultListFields(ListMapper $listMapper): void
    {
        $defaultFields = $this->getDefaultFields('list');
        foreach ($defaultFields as $field => $fieldConfig) {
            $fieldDescriptionOptions = $fieldConfig['fieldDescriptionOptions'] ?? [];
            $listMapper->add($field, $fieldConfig['type'], $fieldDescriptionOptions);
        }
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $this->addDefaultListFields($listMapper);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * Adds the default list actions to the list mapper
     *
     * @param ListMapper $listMapper
     */
    protected function addDefaultListActions(ListMapper $listMapper): void
    {
        $listMapper->add('_action', null, [
            'label' => 'app.common.actions',
            'translation_domain' => 'messages',
            'actions' => [
                'show' => [],
                'edit' => [],
                'delete' => [],
            ]
        ]);
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

    /**
     * Get list of fields to be hidden in the form
     * @return array
     */
    final protected function getFormHideFields(): array
    {
        $hideFields = [];
        $parentFieldDescription = $this->getParentFieldDescription();
        if (null !== $parentFieldDescription) {
            $parentOptions = $parentFieldDescription->getOptions();
            if (!empty($parentOptions['ba_custom_hide_fields'])) {
                $hideFields = $parentOptions['ba_custom_hide_fields'];
            }
        }
        return $hideFields;
    }
}
