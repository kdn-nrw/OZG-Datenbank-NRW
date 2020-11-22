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

use App\Admin\ContextAwareAdminInterface;
use App\Exporter\Source\CustomEntityValueProvider;
use App\Service\ApplicationContextHandler;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdatePropertyAccessorCache
 */
class UpdatePropertyAccessorCache implements EventSubscriberInterface
{

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @param CacheItemPoolInterface $cache
     * @required
     */
    public function injectCache(CacheItemPoolInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SearchIndexEntityEvent::class => [
                ['updateEntityPropertyCache', 20]
            ],
        ];
    }

    /**
     * Delete thumbnails for uploaded images
     *
     * @param SearchIndexEntityEvent $event
     */
    public function updateEntityPropertyCache(SearchIndexEntityEvent $event)
    {
        $entity = $event->getObject();
        $admin = $event->getAdmin();
        if ($admin instanceof ContextAwareAdminInterface) {
            // Update the export value cache
            $exportFields = $admin->getExportFields();
            $customValueProvider = new CustomEntityValueProvider(
                $exportFields,
                $this->cache,
                ApplicationContextHandler::getDefaultAdminApplicationContext($admin),
                'd.m.Y H:i:s'
            );
            $customValueProvider->getCacheItemData($entity, true);
        }
    }
}
