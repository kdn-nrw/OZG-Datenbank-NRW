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

namespace App\Api\Consumer;

use App\Api\Consumer\Model\ZuFiDemand;
use App\Api\Consumer\Model\ZuFiResult;
use App\Api\Form\Type\ZuFiType;

class ZuFiConsumer extends AbstractApiConsumer
{
    private const API_URI = 'https://zufi.api.vsm.nrw/zustaendigkeiten';

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
     * Returns the class name for the result model
     *
     * @return string
     */
    public function getDemandClass(): string
    {
        return ZuFiDemand::class;
    }

    /**
     * Returns the search result template for this consumer
     * @return string
     */
    public function getResultTemplate(): string
    {
        return 'Vsm/Partials/Results/_zu-fi-results.html.twig';
    }

    /**
     * Returns the class name for the search form type
     *
     * @return string
     */
    public function getFormTypeClass(): string
    {
        return ZuFiType::class;
    }

    /**
     * Returns the class name for the result model
     *
     * @return string
     */
    public function getResultModelClass(): string
    {
        return ZuFiResult::class;
    }
}
