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

namespace App\Datagrid;


use App\Sonata\AdminBundle\Datagrid\Datagrid;

/**
 * Class CustomDatagrid
 */
class CustomDatagrid extends Datagrid implements FulltextSearchDatagridInterface
{
    /**
     * Filter items that are displayed in the frontend sidebar menu
     * @var array
     */
    private $filterMenuItems = [];

    /**
     * @var array|null
     */
    private $recordIdList;

    /**
     * Returns true, if the given filter menu is defined
     * @param string $filterName
     * @return bool
     */
    public function hasFilterMenu(string $filterName): bool
    {
        return isset($this->filterMenuItems[$filterName]);
    }

    /**
     * Add the filter information for displaying the menu items
     *
     * @param string $filterName The name of the filter property
     * @param array $filterChoices The filter choices
     * @param string $label The filter label
     * @param string $class The filter entity class
     */
    public function addFilterMenu(string $filterName, array $filterChoices, string $label, string $class): void
    {
        $filterItem = [
            'label' => $label,
            'choices' => $filterChoices,
            'value' => null,
            'valueEntities' => [],
            'class' => $class,
            'parameter' => 'filter' . (new \ReflectionClass($class))->getShortName(),
        ];
        $filterNameInternal = str_replace('.', '__', $filterName);
        $values = $this->getValues();
        if (isset($values[$filterNameInternal]['value'])) {
            $filterItem['value'] = $values[$filterNameInternal]['value'];
            $value = (array)$filterItem['value'];
            if (!empty($value)) {
                foreach ($filterChoices as $entity) {
                    if (in_array($entity->getId(), $value, false)) {
                        $filterItem['valueEntities'][$entity->getId()] = $entity;
                    }
                }
            }
        }
        $this->filterMenuItems[$filterName] = $filterItem;
    }

    /**
     * Returns the filter menu items
     *
     * @return array
     */
    public function getFilterMenuItems(): array
    {
        return $this->filterMenuItems;
    }

    public function setCustomOrderRecordIdList(array $recordIdList)
    {
        $this->recordIdList = $recordIdList;
    }

    public function getResults(): iterable
    {
        $this->buildPager();

        if (null === $this->results) {
            if (null !== $this->recordIdList) {
                $results = $this->getPager()->getCurrentPageResults();

                $sortedResults = [];
                foreach ($this->recordIdList as $recordId) {
                    $sortedResults[$recordId] = null;
                }
                foreach ($results as $offset => $row) {
                    if (method_exists($row, 'getId')) {
                        $sortedResults[$row->getId()] = $row;
                    } else {
                        $sortedResults['_no_id_' . $offset] = $row;
                    }
                }
                $this->results = array_values(array_filter($sortedResults));
            } else {
                $this->results = $this->getPager()->getCurrentPageResults();
            }
        }

        return $this->results;
    }

    /**
     * Update existing values based on defined filters;
     * This is needed in combination with \App\Form\Filter\GroupedSessionFilterPersister to remove values
     * for filters that are not defined in the datagrid
     */
    public function cleanValues()
    {
        $values = $this->getValues();
        if (!empty($values)) {
            $filters = $this->getFilters();
            $filterNames = array_keys($filters);
            $valueNames = array_keys($values);
            foreach ($valueNames as $name) {
                if (strpos($name, '_') !== 0 && !in_array($name, $filterNames, false)) {
                    unset($this->values[$name]);
                }
            }
        }
    }
}