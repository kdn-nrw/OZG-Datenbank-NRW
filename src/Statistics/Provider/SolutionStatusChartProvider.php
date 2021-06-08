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

namespace App\Statistics\Provider;

use App\Entity\Solution;

class SolutionStatusChartProvider extends AbstractForeignNamedPropertyChartProvider
{

    protected $chartLabel = 'Anzahl der Online-Dienste';
    protected $foreignColorProperty = 'color';

    protected function getEntityClass(): string
    {
        return Solution::class;
    }
}