<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Statistics\Provider;

use App\Entity\ImplementationProject;

class ImplementationProjectStatusChartProvider extends AbstractForeignNamedPropertyChartProvider
{

    protected $chartLabel = 'Anzahl der Umsetzungsprojekte';

    protected function getEntityClass(): string
    {
        return ImplementationProject::class;
    }
}