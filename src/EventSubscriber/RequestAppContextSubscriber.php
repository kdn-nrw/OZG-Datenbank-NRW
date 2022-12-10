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

namespace App\EventSubscriber;

use App\Service\InjectApplicationContextHandlerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class RequestAppContextSubscriber
 */
class RequestAppContextSubscriber implements EventSubscriberInterface
{
    use InjectApplicationContextHandlerTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 30]],
        ];
    }

    /**
     * Set the application context depending on the current path (frontend/backend)
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if ($event->isMainRequest()) {
            $pathInfo = $event->getRequest()->getPathInfo();
            $this->applicationContextHandler->setApplicationContextFromPathInfo($pathInfo);
        }
    }
}
