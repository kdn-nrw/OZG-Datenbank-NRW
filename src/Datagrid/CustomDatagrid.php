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


use Sonata\AdminBundle\Datagrid\Datagrid;

/**
 * Class CustomDatagrid
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-09
 */
class CustomDatagrid extends Datagrid
{
    /**
     * Filter items that are displayed in the frontend sidebar menu
     * @var array
     */
    private $filterMenuItems = [];

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
            $value = (array) $filterItem['value'];
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
}