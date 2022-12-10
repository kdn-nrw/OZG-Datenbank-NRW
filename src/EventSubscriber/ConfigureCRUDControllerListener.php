<?php

declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Admin\AbstractAppAdmin;
use App\Admin\AbstractContextAwareAdmin;
use App\Admin\Frontend\ContextFrontendAdminInterface;
use App\Controller\AbstractFrontendCRUDController;
use App\Controller\CommuneAdminController;
use App\Controller\Onboarding\AbstractOnboardingAdminController;
use App\Service\InjectAdminManagerTrait;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\AdminCodeNotFoundException;
use Sonata\AdminBundle\Request\AdminFetcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @internal
 */
final class ConfigureCRUDControllerListener implements EventSubscriberInterface
{
    use InjectAdminManagerTrait;

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if (\is_array($controller)) {
            $controller = $controller[0];
        }

        if (!$controller instanceof CRUDController) {
            return;
        }

        $request = $event->getRequest();

        if ($controller instanceof AbstractFrontendCRUDController) {
            $controller->configureFrontendController($request);
        } elseif ($controller instanceof AbstractOnboardingAdminController) {
            $controller->configureOnboardingController($request);
        } elseif ($controller instanceof CommuneAdminController) {
            $controller->configureCommuneController($request);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => 'onKernelController'];
    }
}
