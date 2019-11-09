<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-09
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
    private $filterMenuItems = [];

    public function addFilterMenu($filterName, $filterChoices, $label) {
        $filterItem = [
            'label' => $label,
            'choices' => $filterChoices,
            'value' => null,
        ];
        $filterNameInternal = str_replace('.', '__', $filterName);
        $values = $this->getValues();
        if (isset($values[$filterNameInternal])) {
            $filterItem['value'] = $values[$filterNameInternal]['value'];
        }
        $this->filterMenuItems[$filterName] = $filterItem;
    }

    public function getFilterMenuItems()
    {
        return $this->filterMenuItems;
    }
}