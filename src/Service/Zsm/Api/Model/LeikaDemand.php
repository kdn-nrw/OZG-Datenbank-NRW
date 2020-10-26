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

namespace App\Service\Zsm\Api\Model;

class LeikaDemand extends AbstractDemand
{
    /**
     * Initialize the parameter registry (list of allowed parameters)
     */
    protected function initializeParameterRegistry(): void
    {
        $this->registerParameter(new DemandParameter('q', 'Suchbegriff', true));
    }
}
