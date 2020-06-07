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

namespace App\Tests\Controller\Admin;

/**
 * Interface for backend tests
 */
interface BackendTestInterface
{
    const BACKEND_URL_PREFIX = '/admin/';
    // Test login data
    const LOGIN_USERNAME = 'info@gerthammes.de';
    const LOGIN_PASSWORD = 'password';
    const SELECTOR_CONTENT_SECTION = '.content-wrapper';
    const FIREWALL_NAME = 'admin';
    const FIREWALL_CONTEXT = 'user';
}