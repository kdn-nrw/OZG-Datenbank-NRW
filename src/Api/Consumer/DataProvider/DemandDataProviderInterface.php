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

namespace App\Api\Consumer\DataProvider;

use App\Api\Consumer\Model\AbstractDemand;

interface DemandDataProviderInterface
{

    /**
     * Returns the API demand instance
     *
     * @return AbstractDemand
     */
    public function getDemand(): AbstractDemand;
}
