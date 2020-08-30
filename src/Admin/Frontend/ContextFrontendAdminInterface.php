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

namespace App\Admin\Frontend;

/**
 * Class ContextFrontendAdminInterface
 */
interface ContextFrontendAdminInterface
{
    /**
     * Initialized the routes and templates for this admin
     */
    public function initializeAppContext(): void;
}
