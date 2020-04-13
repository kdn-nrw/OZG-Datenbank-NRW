<?php

namespace App\Statistics;

/**
 * Loader service for statistics provider
 */
final class ProviderLoader
{

    /**
     * @var AbstractStatisticsProvider[]
     */
    private $registry;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->registry = array();
    }

    public function addProvider(AbstractStatisticsProvider $helper): void
    {
        $this->registry[$helper->getKey()] = $helper;
        $this->registry[get_class($helper)] = $helper;
    }

    /**
     * Returns all providers for the given provider type
     *
     * @param string $type Provider type (excel|csv|chart)
     *
     * @return AbstractStatisticsProvider[]
     */
    public function getProviders($type = null): array
    {
        $providers = [];
        foreach ($this->registry as $providerKey => $provider) {
            if (null === $type || $provider->getType() === $type) {
                $providers[$providerKey] = $provider;
            }
        }
        return $providers;
    }

    /**
     * Returns the provider for the given provider key
     *
     * @param string $providerKey Unique provider key
     *
     * @return AbstractStatisticsProvider|null
     */
    public function getProviderByKey($providerKey): ?AbstractStatisticsProvider
    {
        return $this->registry[$providerKey] ?? null;
    }

}