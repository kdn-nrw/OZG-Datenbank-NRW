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

namespace App\Service\Zsm\Api;

use App\Service\Zsm\Api\Model\AbstractDemand;
use App\Service\Zsm\Api\Model\ZuFiDemand;

class ZuFiProvider extends AbstractApiProvider
{
    private const API_URI = 'https://zufi.api.vsm.nrw/zustaendigkeiten';

    /*
     Die Fremdadapter KiTa (99041004000000) und Finanzämter (99102008000000, 99102015000000, 99102011000000, 99102009000000,
99102016000000) werden ebenfalls in der API berücksichtigt.
     */

    public function getName(): string
    {
        return 'ZuFi-API v1.0.2';
    }

    public function getDescription(): string
    {
        return 'Die ZuFi-API v1.0.2 liefert zu einem Regionalschlüssel oder einer Postleitzahl und zu einem Leistungsschlüssel Zuständigkeiten zurück.';
    }

    /**
     * Returns the API base url
     * @return string
     */
    protected function getApiUrl(): string
    {
        return self::API_URI;
    }

    /**
     * Returns the API demand instance
     * @return AbstractDemand
     */
    public function getDemand(): AbstractDemand
    {
        return new ZuFiDemand();
    }
}
