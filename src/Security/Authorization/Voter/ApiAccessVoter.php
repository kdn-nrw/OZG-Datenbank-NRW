<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security\Authorization\Voter;


use App\Entity\User;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Custom voter for API access, because we can't use the roles (not set in token)
 * @see \App\Security\Guard\JWTTokenAuthenticator
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 */
class ApiAccessVoter extends Voter
{
    const API_ONBOARDING_READ = 'PERMISSION_API_ONBOARDING';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if ($attribute !== self::API_ONBOARDING_READ) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var UserInterface|User $targetUser */
        $mapAttributeToRole = str_replace('PERMISSION_', 'ROLE_', $attribute);
        $userRoles = $user->getRoles();
        if (in_array($mapAttributeToRole, $userRoles, false)) {
            return true;
        }
        // Compare lower case without backslashes and underscore
        $cmpAttribute = strtolower(str_replace(['\\', '_'], '', $attribute));
        foreach ($userRoles as $role) {
            if (strtolower(str_replace(['\\', '_'], '', $role)) === $cmpAttribute) {
                return true;
            }
        }
        return false;
    }
}
