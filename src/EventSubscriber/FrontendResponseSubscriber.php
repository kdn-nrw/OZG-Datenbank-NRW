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

use App\Service\InjectApplicationContextHandlerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/*
 * Class FrontendResponseSubscriber
 */

class FrontendResponseSubscriber implements EventSubscriberInterface
{
    use InjectApplicationContextHandlerTrait;

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => ['onKernelResponse', -50],
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$this->applicationContextHandler->isBackend()) {
            $response = $event->getResponse();
            $content = $response->getContent();
            if ($content) {
                $parsedContent = str_replace('"USE_STICKYFORMS":true', '"USE_STICKYFORMS":false', $content);
                $removeScripts = ['sidebar', 'markitup', 'markdown', 'masonry'];
                foreach ($removeScripts as $script) {
                    $parsedContent = preg_replace('/<script.*' . $script . '.*<\/script>\s+/', '', $parsedContent);
                }
                $response->setContent($parsedContent);
            }
        }
    }


}
