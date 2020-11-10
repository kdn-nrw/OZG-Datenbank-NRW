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

/**
 * Injection trait for ApiManager
 * @package App\Api\Consumer
 */
trait InjectApiManagerTrait
{
    /**
     * @var ApiManager
     */
    protected $apiManager;

    /**
     * @required
     * @param ApiManager $apiManager
     */
    public function injectApiManager(ApiManager $apiManager): void
    {
        $this->apiManager = $apiManager;
    }

}
