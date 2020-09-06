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

namespace App\Tests\Controller\Frontend;

use PHPUnit\Framework\Constraint\GreaterThan;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional test for the frontend sidebar.
 */
class SidebarTest extends WebTestCase implements FrontendTestInterface
{
    public function testGlobalSearch()
    {
        $client = self::createClient();
        $client->catchExceptions(false);
        $client->request('GET', '/');
        self::assertResponseIsSuccessful();
        $crawler = $client->submitForm('global-search-submit', [
            'q' => 'Baugenehmigung',
        ]);

        $crawlerContent = $crawler->filter(self::SELECTOR_CONTENT_SECTION)->first();
        static::assertThat(
            $crawlerContent->filter('.search-box-item')->count(),
            new GreaterThan(0),
            'The view contains at least 1 search result box.'
        );
    }
    public function testSidbarMenu()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $crawler = $client->request('GET', '/');
        self::assertResponseIsSuccessful();
        $crawlerSidebar = $crawler->filter('.main-sidebar')->first();
        $navLinkInfo = $crawlerSidebar->filter('a.nav-link')->extract(['href']);
        self::assertThat(
            count($navLinkInfo),
            new GreaterThan(0),
            'The sidebar menu contains at least 1 item.'
        );
        foreach ($navLinkInfo as $url) {
            if (strpos($url, '#') !== 0) {
                $navPageCrawler = $client->request('GET', $url);
                self::assertResponseIsSuccessful();
                self::assertThat(
                    $navPageCrawler->filter('.main-sidebar')->count(),
                    new GreaterThan(0),
                    'The navigation page contains a sidebar.'
                );
            }
        }
    }

    public function testSidebarFilter()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $crawler = $client->request('GET', '/ozg-leistungen');
        self::assertResponseIsSuccessful();
        $crawlerSidebar = $crawler->filter('.main-sidebar')->first();
        $filterLinkCrawler = $crawlerSidebar->filter('a.custom-menu-filter-link');
        foreach ($filterLinkCrawler as $elementCrawler) {

            $urlAttr = $elementCrawler->attributes->getNamedItem('data-url');
            $filterValueAttr = $elementCrawler->attributes->getNamedItem('data-filter-value');
            $urlTemplate = $urlAttr ? $urlAttr->nodeValue : null;
            $filterValue = $filterValueAttr ? $filterValueAttr->nodeValue : null;
            self::assertNotEmpty(
                $urlTemplate,
                'The filter link has a data url.'
            );
            self::assertNotEmpty(
                $filterValue,
                'The filter link has a filter value.'
            );
            $url = str_replace('', $filterValue, $urlTemplate);
            $filterCrawler = $client->request('GET', $url);
            self::assertResponseIsSuccessful();

            self::assertThat(
                $filterCrawler->filter('.sonata-filter-form')->count(),
                new GreaterThan(0),
                'The filter result contains a filter form.'
            );
        }
    }
}