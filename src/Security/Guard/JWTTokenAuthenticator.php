<?php
namespace App\Security\Guard;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class JWTTokenAuthenticator
 * Create token with group names instead of role names, so the JWT size will not exceed the limit
 * @package App\Security\Guard
 */
class JWTTokenAuthenticator extends BaseAuthenticator
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var TokenStorageInterface
     */
    private $preAuthenticationTokenStorage;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        TokenExtractorInterface $tokenExtractor,
        TokenStorageInterface $preAuthenticationTokenStorage
    ) {
        parent::__construct($jwtManager, $dispatcher, $tokenExtractor, $preAuthenticationTokenStorage);
        $this->preAuthenticationTokenStorage = $preAuthenticationTokenStorage;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If there is no pre-authenticated token previously stored
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        $preAuthToken = $this->preAuthenticationTokenStorage->getToken();

        if (null === $preAuthToken) {
            throw new \RuntimeException('Unable to return an authenticated token since there is no pre authentication token.');
        }

        // Use group names in token instead of roles to prevent token size being too big
        // Warning: Because of this the authorization check for roles is always false, because the RoleHierarchyVoter
        // uses the roles set in the token, NOT the actual user roles
        // \Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter
        // \Symfony\Component\Security\Core\Authorization\AccessDecisionManager::decideUnanimous
        // This means to check the api permissions, use custom voter attributes:
        // \App\Security\Authorization\Voter\ApiAccessVoter
        /** @var User $user */
        $groups = [];
        foreach ($user->getGroups() as $group) {
            $groups[] = $group->getName();
        }
        $authToken = new JWTUserToken($groups, $user, $preAuthToken->getCredentials(), $providerKey);

        $this->dispatcher->dispatch(new JWTAuthenticatedEvent($preAuthToken->getPayload(), $authToken), Events::JWT_AUTHENTICATED);

        $this->preAuthenticationTokenStorage->setToken();

        return $authToken;
    }
}