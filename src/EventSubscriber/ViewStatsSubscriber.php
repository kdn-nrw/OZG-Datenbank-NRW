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

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Statistics\LogEntry;
use Doctrine\ORM\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

/**
 * Class ViewStatsSubscriber
 */
class ViewStatsSubscriber implements EventSubscriberInterface
{
    use InjectManagerRegistryTrait;

    /**
     * @var Security
     */
    private $security;

    /**
     * ViewStatsSubscriber constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    /**
     * Log view before response is sent
     *
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if (!($response instanceof RedirectResponse)) {
            $request = $event->getRequest();
            $route = $request->attributes->get('_route');
            $excludeRoutePrefixes = [
                '_wdt',
                'admin_app_user',
                'admin_app_group',
                'admin_app_api_apiconsumer',
                'admin_app_metadata_metaitem',
                'app_statistics_chart',
            ];
            $logView = true;
            foreach ($excludeRoutePrefixes as $checkRoutePrefix) {
                if (strpos($route, $checkRoutePrefix) !== false) {
                    $logView = false;
                    break;
                }
            }
            if ($logView) {
                $this->createViewEntry($request);
            }
        }
    }

    /**
     * Creates a view entry
     *
     * @param Request $request
     */
    private function createViewEntry(Request $request): void
    {
        $route = $request->attributes->get('_route');
        $logEntry = new LogEntry();
        if (null !== $this->security && null !== $user = $this->security->getUser()) {
            $logEntry->setUser($user);
        }
        $logEntry->setPathInfo($request->getPathInfo());
        $logEntry->setRequestMethod($request->getMethod());
        $logEntry->setQueryParameters($request->query->all());
        $logEntry->setRequestAttributes($request->attributes->all());
        $logEntry->setRoute($route);
        $logEntry->setRequestLocale($request->getLocale());
        $em = $this->getEntityManager();
        try {
            $em->persist($logEntry);
            $em->flush();
            // Ignore exceptions so the response can be sent normally
        } catch (ORMException $e) {
            unset($e);
        }
    }
}
