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

use App\Model\EmailTemplate\AbstractTemplateModel;
use App\Service\Mailer\InjectEmailTemplateManagerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SendNotificationsAfterUpdateSubscriber
 */
class SendNotificationsAfterUpdateSubscriber implements EventSubscriberInterface
{
    use InjectEmailTemplateManagerTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            EntityPostUpdateEvent::class => [
                ['afterEntityUpdated', -20]
            ],
            EntityPostCreateEvent::class => [
                ['afterEntityCreate', -20]
            ],
        ];
    }

    /**
     * After entity update
     *
     * @param EntityPostUpdateEvent $event
     */
    public function afterEntityUpdated(EntityPostUpdateEvent $event): void
    {
        $this->emailTemplateManager->sendNotificationsForObject($event->getObject(), AbstractTemplateModel::PROCESS_TYPE_UPDATE);
    }

    /**
     * After entity created
     *
     * @param EntityPostCreateEvent $event
     */
    public function afterEntityCreate(EntityPostCreateEvent $event): void
    {
        $this->emailTemplateManager->sendNotificationsForObject($event->getObject(), AbstractTemplateModel::PROCESS_TYPE_CREATE);
    }
}
