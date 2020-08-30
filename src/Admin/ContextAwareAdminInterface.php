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

namespace App\Admin;


use App\Model\ReferenceSettings;
use App\Service\ApplicationContextHandler;

/**
 * Class ContextAwareAdminInterface
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-04-26
 */
interface ContextAwareAdminInterface
{
    /**
     * Returns the default reference settings for the reference lists in the detail views of other admins
     *
     * @param ApplicationContextHandler $applicationContextHandler The application context handler
     * @param string $editRouteName The edit route may be overridden in the field configuration
     * @return ReferenceSettings
     */
    public function getReferenceSettings(
        ApplicationContextHandler $applicationContextHandler,
        string $editRouteName = 'edit'): ReferenceSettings;
}
