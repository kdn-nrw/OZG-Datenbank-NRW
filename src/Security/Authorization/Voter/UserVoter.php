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

namespace App\Security\Authorization\Voter;


use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserVoter
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 */
class UserVoter extends Voter
{
    protected const VIEW = 'ROLE_SONATA_USER_ADMIN_USER_VIEW';
    protected const EDIT = 'ROLE_SONATA_USER_ADMIN_USER_EDIT';

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on User objects inside this voter
        if (!$subject instanceof UserInterface) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var UserInterface $targetUser */
        $targetUser = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($targetUser, $user);
            case self::EDIT:
                return $this->canEdit($targetUser, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(UserInterface $targetUser, UserInterface $user): bool
    {
        // if they can edit, they can view
        return $this->canEdit($targetUser, $user);
    }

    private function canEdit(UserInterface $targetUser, UserInterface $user): bool
    {
        return $user === $targetUser;
    }
}
