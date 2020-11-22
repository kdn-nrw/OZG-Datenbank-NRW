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

declare(strict_types=1);

namespace App\Admin\Extension;

use App\Service\InjectApplicationContextHandlerTrait;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Admin extension for configuring routes in the frontend
 */
class FrontendRoutingExtension extends AbstractAdminExtension
{
    use InjectApplicationContextHandlerTrait;

    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        // Don't remove the routes because this may cause problems with the caching in the backend
        /*if (!$this->applicationContextHandler->isBackend()) {
            $collection->clearExcept(['list', 'show', 'export']);
        }*/
    }
}
