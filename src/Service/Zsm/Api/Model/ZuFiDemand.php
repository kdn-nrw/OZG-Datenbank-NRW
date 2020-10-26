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

class ZuFiDemand extends AbstractDemand
{
    /*
     Die Fremdadapter KiTa (99041004000000) und Finanzämter (99102008000000, 99102015000000, 99102011000000, 99102009000000,
99102016000000) werden ebenfalls in der API berücksichtigt.
     */

    /**
     * Initialize the parameter registry (list of allowed parameters)
     */
    protected function initializeParameterRegistry(): void
    {
        //$this->registerParameter(new DemandParameter('suchbegriff', 'Suchbegriff', true));
        $this->registerParameter(new DemandParameter('leistungsSchluessel', 'Leistungsschlüssel', true));
        $this->registerParameter(new DemandParameter('regionalSchluessel', 'Regionalschlüssel', false));
        $this->registerParameter(new DemandParameter('plz', 'PLZ', false));
    }
}
