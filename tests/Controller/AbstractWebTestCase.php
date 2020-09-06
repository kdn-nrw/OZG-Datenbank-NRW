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
     * Parse links in content of given crawler and return links grouped by view
     * Currently only used for show and export view
     *
     * @param Crawler $crawler
     * @param string $assertNotStartsWith
     * @return array
     */
    protected function parseLinks(Crawler $crawler, string $assertNotStartsWith = '/admin/'): array
    {
        $testViewData = [];
        $linkInfo = $crawler->filter('a')->extract(['href']);
        $routePrefix = $this->getRoutePrefix();
        $internalPaths = [];
        foreach ($linkInfo as $link) {
            $urlParts = parse_url($link);
            if (!array_key_exists('path', $urlParts) || empty($urlParts['path'])) {
                $urlParts['path'] = $link;
            }
            $path = $urlParts['path'];
            if ($assertNotStartsWith) {
                $pos = strpos($path, $assertNotStartsWith);
                // No links to admin backend in content section in frontend
                self::assertTrue($pos === false || $pos < 1);
            }
            $pos = strpos($path, $routePrefix);
            if ($pos !== false) {
                $internalPaths[] = $urlParts;
            }
        }
        shuffle($internalPaths);
        $showPattern = '/\/' . preg_quote($this->getRoutePrefix(), '/') . '\/details\/([\w\d\-]+)/';
        $defaultRoutePattern = '/\/' . preg_quote($this->getRoutePrefix(), '/') . '(\/(\d+))?(\/(\w+))?/';
        foreach ($internalPaths as $urlParts) {
            $path = $urlParts['path'];
            if (strpos($path, '/details/') !== false) {
                if (preg_match($showPattern, $path, $matches)) {
                    $view = 'show';
                    $testViewData[$view][$matches[1]] = $matches[1];
                }
            } elseif (preg_match($defaultRoutePattern, $path, $matches)) {
                $view = $matches[4] ?? 'list';
                if (!empty($matches[2])) {
                    $testViewData[$view][$matches[2]] = $matches[2];
                } elseif (array_key_exists('query', $urlParts)) {
                    $testViewData[$view][] = $urlParts['query'];
                } else {
                    $testViewData[$view][] = null;
                }
            }
        }
        return $testViewData;
    }
}