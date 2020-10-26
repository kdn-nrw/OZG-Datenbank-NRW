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
use App\Service\Zsm\Api\Model\ArsAgsDemand;

class ArsAgsProvider extends AbstractApiProvider
{
    private const API_URI = 'https://ags-ars.api.vsm.nrw/orte';

    public function getName(): string
    {
        return 'ARS/AGS-API v1.0';
    }

    public function getDescription(): string
    {
        return 'Mithilfe der ARS/AGS-API v1.0 können amtlicher Gemeindeschlüssel (AGS) und amtlicher Regionalschlüssel (ARS) von Orten oder Gebieten ermittelt
werden.';
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
        return new ArsAgsDemand();
    }
}
