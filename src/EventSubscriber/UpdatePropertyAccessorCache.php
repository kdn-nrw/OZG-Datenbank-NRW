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

use App\Exporter\Source\CustomEntityValueProvider;
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
        $entity = $event->getObject();
        $admin = $event->getAdmin();
        $dataSourceIterator = $admin->getDataSourceIterator();
        if ($dataSourceIterator instanceof CustomEntityValueProvider) {
            $dataSourceIterator->getCacheItemData($entity, true);
        }
    }
}
