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

use App\Admin\CustomExportAdminInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdatePropertyAccessorCache
 */
class UpdatePropertyAccessorCache implements EventSubscriberInterface
{
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
     * Update property value cache for entity
     *
     * @param SearchIndexEntityEvent $event
     */
    public function updateEntityPropertyCache(SearchIndexEntityEvent $event): void
    {
        $admin = $event->getAdmin();
        if ($admin instanceof CustomExportAdminInterface) {
            $entity = $event->getObject();
            $dataSourceIterator = $admin->getCustomDataSourceIterator();
            $dataSourceIterator->updateCacheItemData($entity);
        }
    }
}
