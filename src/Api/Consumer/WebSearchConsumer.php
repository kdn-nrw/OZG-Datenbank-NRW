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

use App\Api\Consumer\Model\WebSearchDemand;
use App\Api\Consumer\Model\WebSearchResult;
use App\Api\Form\Type\WebSearchType;

class WebSearchConsumer extends AbstractApiConsumer
{
    private const API_URI = 'https://web-suche.api.vsm.nrw/web-treffer';

    //private const API_URI = 'https://suche.api.vsm.nrw/suche';

    public function getName(): string
    {
        return 'Web-Such-API v1.0';
    }

    public function getDescription(): string
    {
        return 'Die Web-Such-API liefert zu einem Suchbegriff Volltext-Treffer von den Websites, die vom Crawler der VSM durchsucht worden sind. Sie liefert keine
Zuständigkeitsinformationen.';
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
        return WebSearchDemand::class;
    }

    /**
     * Returns the search result template for this consumer
     * @return string
     */
    public function getResultTemplate(): string
    {
        return 'Vsm/Partials/Results/_web-search-results.html.twig';
    }

    /**
     * Returns the class name for the search form type
     *
     * @return string
     */
    public function getFormTypeClass(): string
    {
        return WebSearchType::class;
    }

    /**
     * Returns the class name for the result model
     *
     * @return string
     */
    public function getResultModelClass(): string
    {
        return WebSearchResult::class;
    }
}
