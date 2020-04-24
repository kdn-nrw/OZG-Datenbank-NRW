<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Statistics;

/**
 * Statistics provider manager
 */
final class StatisticsManager
{
    /**
     * @var ProviderLoader
     */
    private $providerLoader;

    /**
     * Constructor
     * @param ProviderLoader $providerLoader
     */
    public function __construct(ProviderLoader $providerLoader)
    {
        $this->providerLoader = $providerLoader;
    }

    /**
     * Returns all providers for the given provider type
     *
     * @return AbstractStatisticsProvider[][]
     */
    public function getGroupedProviders()
    {
        $groupedProviders = [
            'chart' => [],
            'excel' => [],
            'csv' => [],
        ];
        $providers = $this->providerLoader->getProviders();
        foreach ($providers as $provider) {
            $groupedProviders[$provider->getType()][] = $provider;
        }
        return $groupedProviders;
    }

    /**
     * Returns the provider for the given provider key
     *
     * @param string $providerKey Unique provider key
     *
     * @return AbstractStatisticsProvider
     */
    public function getProviderByKey($providerKey)
    {
        return $this->providerLoader->getProviderByKey($providerKey);
    }

}