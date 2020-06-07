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

namespace App\Tests\Controller;

use PHPUnit\Framework\Constraint\GreaterThan;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Functional test for the controllers defined inside frontend admin controllers.
 *
 * See https://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ ./vendor/bin/phpunit
 */
abstract class AbstractWebTestCase extends WebTestCase
{

    abstract protected function getRoutePrefix(): string;

    /**
     * Global url prefix depending on context (backend/frontend)
     * @return string
     */
    protected function getContextPrefix(): string
    {
        return '';
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    protected function getRouteUrl(string $view, array $params = []): string
    {
        $route = $this->getRoutePrefix();
        if ($view !== 'list' && $view !== 'index') {
            if (array_key_exists('id', $params)) {
                $route .= '/' . $params['id'];
            }
            return $route . '/' . $view;
        }
        return $this->getContextPrefix() . $route;
    }
}