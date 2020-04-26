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


/**
 * Class ContextAwareAdminInterface
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-04-26
 */
interface ContextAwareAdminInterface
{
    public const APP_CONTEXT_BE = 'backend';
    public const APP_CONTEXT_FE = 'frontend';

    /**
     * @return string
     */
    public function getAppContext(): string;
}
