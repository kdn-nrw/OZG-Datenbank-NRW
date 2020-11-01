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

use App\Api\Consumer\Model\AbstractDemand;
use App\Api\Consumer\Model\ArsAgsDemand;
use App\Api\Consumer\Model\ArsAgsResult;
use App\Api\Consumer\Model\ResultCollection;
use App\Api\Form\Type\ArsAgsSearchType;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ArsAgsConsumer extends AbstractApiConsumer
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
     * Returns the class name for the result model
     *
     * @return string
     */
    public function getDemandClass(): string
    {
        return ArsAgsDemand::class;
    }

    /**
     * Returns the search result template for this consumer
     * @return string
     */
    public function getResultTemplate(): string
    {
        return 'Vsm/Partials/Results/_ars-ags-results.html.twig';
    }

    /**
     * Returns the class name for the search form type
     *
     * @return string
     */
    public function getFormTypeClass(): string
    {
        return ArsAgsSearchType::class;
    }

    /**
     * Returns the class name for the result model
     *
     * @return string
     */
    public function getResultModelClass(): string
    {
        return ArsAgsResult::class;
    }
}
