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
use App\Service\Zsm\Api\Model\WebSearchDemand;

class WebSearchProvider extends AbstractApiProvider
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
     * Returns the API demand instance
     * @return AbstractDemand
     */
    public function getDemand(): AbstractDemand
    {
        return new WebSearchDemand();
    }
}
