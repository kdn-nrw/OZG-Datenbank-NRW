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

use App\Api\Consumer\Model\LeikaDemand;
use App\Api\Consumer\Model\LeikaResult;
use App\Api\Form\Type\LeikaSearchType;

class LeikaConsumer extends AbstractApiConsumer
{
    private const API_URI = 'https://leika.vsm.nrw/services';

    public function getName(): string
    {
        return 'LeiKa-API v1.0';
    }

    public function getDescription(): string
    {
        return 'Die LeiKa API v1.0 liefert zu einem eingegebenen Leistungsbegriff relevante Informationen zurück.';
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
        return LeikaDemand::class;
    }

    /**
     * Returns the search result template for this consumer
     * @return string
     */
    public function getResultTemplate(): string
    {
        return 'Vsm/Partials/Results/_leika-results.html.twig';
    }

    /**
     * Returns the class name for the search form type
     *
     * @return string
     */
    public function getFormTypeClass(): string
    {
        return LeikaSearchType::class;
    }

    /**
     * Returns the class name for the result model
     *
     * @return string
     */
    public function getResultModelClass(): string
    {
        return LeikaResult::class;
    }
}
