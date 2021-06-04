<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Entity\Group;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class JWTCreatedSubscriber
 */
class JWTCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            \Lexik\Bundle\JWTAuthenticationBundle\Events::JWT_CREATED => [['onJWTCreated', 30]],
        ];
    }

    /**
     * Replace roles in payload to prevent token size being too big
     * @param JWTCreatedEvent $event
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $data = $event->getData();
        if (array_key_exists('roles', $data)) {
            unset($data['roles']);
        }
        /** @var User $user */
        $user = $event->getUser();
        $data['groups'] = [];
        $groups = $user->getGroups();
        foreach ($groups as $group) {
            /** @var Group $group */
            $data['groups'] = $group->getName();
        }
        $event->setData($data);
    }
}
