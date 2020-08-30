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

namespace App\Service;

use App\Admin\Frontend\ContextFrontendAdminInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class ApplicationContextHandler
{
    /**
     * The application context for backend requests
     */
    public const APP_CONTEXT_BE = 'backend';

    /**
     * The application context for frontend requests
     */
    public const APP_CONTEXT_FE = 'frontend';

    /**
     * @var string
     */
    private $applicationContext = self::APP_CONTEXT_BE;

    /**
     * @return string
     */
    public function getApplicationContext(): string
    {
        return $this->applicationContext;
    }

    /**
     * @param string $applicationContext
     */
    public function setApplicationContext(string $applicationContext): void
    {
        $this->applicationContext = $applicationContext;
    }

    /**
     * Returns true if the current application context is "backend"
     * @return bool
     */
    public function isBackend()
    {
        return $this->applicationContext === self::APP_CONTEXT_BE;
    }

    /**
     * Set the application context depending on the current path (frontend/backend)
     *
     * @param string $pathInfo
     */
    public function setApplicationContextFromPathInfo(string $pathInfo): void
    {
        if (strpos($pathInfo, '/admin') === 0) {
            $this->applicationContext = self::APP_CONTEXT_BE;
        } else {
            $this->applicationContext = self::APP_CONTEXT_FE;
        }
    }

    /**
     * Returns the list of available application contexts
     * @return array|string[]
     */
    public function getAllContexts(): array
    {
        return [
            self::APP_CONTEXT_FE,
            self::APP_CONTEXT_BE
        ];
    }

    /**
     * Returns the default application context for the given admin
     *
     * @param AdminInterface $admin
     * @return string
     */
    public static function getDefaultAdminApplicationContext(AdminInterface $admin): string
    {
        if ($admin instanceof ContextFrontendAdminInterface) {
            $appContext = self::APP_CONTEXT_FE;
        } else {
            $appContext = self::APP_CONTEXT_BE;
        }
        return $appContext;
    }

}