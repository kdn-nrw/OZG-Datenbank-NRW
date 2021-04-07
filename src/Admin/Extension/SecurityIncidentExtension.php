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

use App\DependencyInjection\InjectionTraits\InjectSecurityTrait;
use App\Entity\StateGroup\SecurityIncident;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * Admin extension for configuring routes in the frontend
 */
class SecurityIncidentExtension extends AbstractAdminExtension
{
    use InjectSecurityTrait;

    public function alterNewInstance(AdminInterface $admin, $object)
    {
        /** @var SecurityIncident $object */
        $user = $this->security->getUser();
        if (null !== $user) {
            $object->setCreatedBy($user);
        }
    }
}
