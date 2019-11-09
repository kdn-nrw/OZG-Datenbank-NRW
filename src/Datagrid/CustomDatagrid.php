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
        $this->filterMenuItems[$filterName] = [
            'label' => $label,
            'choices' => $filterChoices,
        ];
    }

    public function getFilterMenuItems()
    {
        return $this->filterMenuItems;
    }
}