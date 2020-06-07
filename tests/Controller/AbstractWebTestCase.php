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
     * Parse links in content of given crawler and return links grouped by view
     * Currently only used for show and export view
     *
     * @param Crawler $crawler
     * @param string $assertNotContains
     * @return array
     */
    protected function parseLinks(Crawler $crawler, string $assertNotContains = '/admin/'): array
    {
        $testViewData = [];
        $linkInfo = $crawler->filter('a')->extract(['href']);
        shuffle($linkInfo);
        $routePattern = '/\/?' . preg_quote($this->getRoutePrefix(), '/') . '(\/(\d+))?(\/(\w+))?/';
        foreach ($linkInfo as $link) {
            if ($assertNotContains) {
                // No links to admin backend in content section in frontend
                $this->assertNotContains($assertNotContains, $link);
            }
            $urlParts = parse_url($link);
            $path = array_key_exists('path', $urlParts) ? $urlParts['path'] : $link;
            if (preg_match($routePattern, $path, $matches)) {
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