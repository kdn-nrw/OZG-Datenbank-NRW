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

use App\Api\Consumer\Exception\ApiConsumerNotFoundException;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Api\ApiConsumer;

class ApiManager
{
    public const API_KEY_ZU_FI = 'zu_fi';
    public const API_KEY_ARS_AGS = 'ars_ags';

    use InjectManagerRegistryTrait;

    /**
     * @var ApiConsumerInterface[]|array
     */
    protected $consumers = [];

    /**
     * @param ApiConsumerInterface $provider
     * @return void
     */
    public function addConsumer(ApiConsumerInterface $provider): void
    {
        $this->consumers[$provider->getImportSourceKey()] = $provider;
    }

    /**
     * Returns the consumer identified by the given key
     *
     * @param string $consumerKey
     *
     * @return ApiConsumerInterface|null
     */
    private function getConsumer(string $consumerKey): ?ApiConsumerInterface
    {
        return $this->consumers[$consumerKey] ?? null;
    }

    /**
     * @param ApiConsumerInterface[]|array $consumers
     */
    public function setConsumers(array $consumers): void
    {
        $this->consumers = $consumers;
    }

    /**
     * Returns the consumer choices
     *
     * @return string[]|array
     */
    public function getConsumerChoices(): array
    {
        $choices = [];
        foreach (array_keys($this->consumers) as $consumerKey) {
            $choices[$consumerKey] = $consumerKey;
        }
        return $choices;
    }

    /**
     * Returns the configured consumers
     *
     * @param bool $showAll Show all consumers or only publicly visible?
     * @return ApiConsumerInterface[]|array
     * @throws ApiConsumerNotFoundException
     */
    public function getConfiguredConsumers(bool $showAll = false): array
    {
        $consumerRepository = $this->getEntityManager()->getRepository(ApiConsumer::class);
        $consumerEntities = $consumerRepository->findAll();
        $configuredConsumers = [];
        foreach ($consumerEntities as $consumerEntity) {
            /** @var ApiConsumer $consumerEntity */
            $consumerService = $this->getConfiguredConsumer(
                $consumerEntity->getConsumerKey(),
                $consumerEntity,
                $showAll
            );
            if (null !== $consumerService) {
                $configuredConsumers[$consumerService->getImportSourceKey()] = $consumerService;
            }
        }
        return $configuredConsumers;
    }

    /**
     * Returns the configured consumer identified by the given key
     *
     * @param string $consumerKey
     *
     * @param ApiConsumer|null $consumerEntity
     * @param bool $showAll Show all consumers or only publicly visible?
     * @return ApiConsumerInterface|null Returns null if the API is not accessible
     * @throws ApiConsumerNotFoundException
     */
    public function getConfiguredConsumer(string $consumerKey, ApiConsumer $consumerEntity = null, bool $showAll = true): ?ApiConsumerInterface
    {
        if (null === $consumerEntity) {
            $consumerRepository = $this->registry->getRepository(ApiConsumer::class);
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $consumerEntity = $consumerRepository->findOneBy(['consumerKey' => $consumerKey]);
            /** @var ApiConsumer|null $consumerEntity */
        }
        $consumerService = $this->getConsumer($consumerKey);
        if (null === $consumerEntity || null === $consumerService) {
            throw new ApiConsumerNotFoundException(sprintf('No consumer service found for key %s', $consumerEntity->getConsumerKey()));
        }
        if ($showAll || $consumerEntity->isShowInFrontend()) {
            $consumerService->setApiConsumerEntity($consumerEntity);
            return $consumerService;
        }
        return null;
    }

}
