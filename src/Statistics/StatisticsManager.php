<?php
/**
 * Mindbase 3
 *
 * PHP version 5.6
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2018 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      http://www.brain-appeal.com/
 * @since     2018-01-22
 */

namespace App\Statistics;

/**
 * Statistics provider manager
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2018 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      http://www.brain-appeal.com/
 * @since     2018-01-22
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