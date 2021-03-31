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

namespace App\DependencyInjection\InjectionTraits;

use Symfony\Component\Security\Core\Security;

trait InjectSecurityTrait
{
    /**
     * @var Security
     */
    protected $security;

    /**
     * @required
     * @param Security $security
     */
    public function injectSecurity(Security $security): void
    {
        $this->security = $security;
    }
}