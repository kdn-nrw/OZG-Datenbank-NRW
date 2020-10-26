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

namespace App\Service\Zsm;

use App\Service\Zsm\Api\ApiProviderInterface;

class ApiHandler
{
    /**
     * @var ApiProviderInterface[]|array
     */
    protected $providers = [];

    /**
     * @param ApiProviderInterface $provider
     * @return void
     */
    public function addProvider(ApiProviderInterface $provider): void
    {
        $this->providers[$provider->getKey()] = $provider;
    }

    /**
     * @return ApiProviderInterface[]|array
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @param ApiProviderInterface[]|array $providers
     */
    public function setProviders(array $providers): void
    {
        $this->providers = $providers;
    }


}
