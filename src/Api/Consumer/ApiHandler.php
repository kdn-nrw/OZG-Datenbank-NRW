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

class ApiHandler
{
    /**
     * @var ApiConsumerInterface[]|array
     */
    protected $consumers = [];

    /**
     * @param ApiConsumerInterface $provider
     * @return void
     */
    public function addProvider(ApiConsumerInterface $provider): void
    {
        $this->consumers[$provider->getKey()] = $provider;
    }

    /**
     * @return ApiConsumerInterface[]|array
     */
    public function getConsumers(): array
    {
        return $this->consumers;
    }

    /**
     * @param ApiConsumerInterface[]|array $consumers
     */
    public function setConsumers(array $consumers): void
    {
        $this->consumers = $consumers;
    }


}
