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

namespace App\Twig\Extension;

use App\Api\Consumer\ApiManager;
use App\Api\Consumer\Exception\ApiConsumerNotFoundException;
use App\Api\Consumer\InjectApiManagerTrait;
use App\Entity\Api\ServiceBaseResult;
use App\Entity\Service;
use App\Entity\StateGroup\Commune;
use App\Service\InjectApplicationContextHandlerTrait;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ApiQueryExtension extends AbstractExtension
{
    use InjectApiManagerTrait;

    use InjectApplicationContextHandlerTrait;

    /**
     * Store the accessibility state for the API keys
     * @var array|bool[]
     */
    private $apiAccessStorage = [];

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_get_api_arguments', [$this, 'getApiArguments']),
        ];
    }

    /**
     * Returns the query parameter string for api calls
     *
     * @param string $apiIdentifier The api identifier
     * @param Commune|Service $parent
     * @param Service|null $service
     * @return array|null
     */
    public function getApiArguments(string $apiIdentifier, $parent, ?Service $service): ?array
    {
        if ($this->hasGeneralApiAccess($apiIdentifier)) {
            $query = null;
            if ($apiIdentifier === ApiManager::API_KEY_ZU_FI) {
                $serviceKey = null;
                $regionalKey = \App\Api\Consumer\ZuFiConsumer::DEFAULT_REGIONAL_KEY;
                if ($parent instanceof Commune || $parent instanceof ServiceBaseResult) {
                    $regionalKey = $parent->getRegionalKey();
                }
                if (null !== $service) {
                    $serviceKey = $service->getServiceKey();
                } elseif ($parent instanceof Service) {
                    $serviceKey = $parent->getServiceKey();
                } elseif ($parent instanceof ServiceBaseResult) {
                    $serviceKey = $parent->getServiceKey();
                }
                if ($serviceKey && $regionalKey) {
                    $query = $serviceKey . '|' . $regionalKey;
                }
            }
            if ($query) {
                return [
                    'consumerKey' => $apiIdentifier,
                    'query' => $query,
                ];
            }
        }
        return null;
    }

    /**
     * Determine if the api is accessible depending on the mode (backend or frontend)
     * @param string $apiIdentifier The api identifier
     * @return bool
     */
    private function hasGeneralApiAccess(string $apiIdentifier): bool
    {
        $isBackendMode = $this->applicationContextHandler->isBackend();
        $internalKey = $apiIdentifier . '_' . ($isBackendMode ? 'B' : 'F');
        if (!array_key_exists($internalKey, $this->apiAccessStorage)) {
            if ($isBackendMode) {
                try {
                    $hasAccess = $this->authorizationChecker->isGranted('ROLE_VSM');
                // Catch exception on console: The token storage contains no authentication token.
                } catch (AuthenticationCredentialsNotFoundException $e) {
                    $hasAccess = false;
                }
            } else {
                try {
                    $consumer = $this->apiManager->getConfiguredConsumer($apiIdentifier, null, false);
                    $hasAccess = null !== $consumer;
                } catch (ApiConsumerNotFoundException $e) {
                    $hasAccess = false;
                }
            }
            $this->apiAccessStorage[$internalKey] = $hasAccess;
        }
        return $this->apiAccessStorage[$internalKey];
    }
}
