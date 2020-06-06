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

/**
 * Functional test for the frontend sidebar.
 */
class SidebarTest extends WebTestCase
{
    public function testGlobalSearch()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        self::assertResponseIsSuccessful();
        $crawler = $client->submitForm('global-search-submit', [
            'q' => 'Baugenehmigung',
        ]);

        $crawlerContent = $crawler->filter('.content-wrapper')->first();
        static::assertThat(
            $crawlerContent->filter('.search-box-item')->count(),
            new GreaterThan(1),
            'The view contains at least 1 search result box.'
        );
    }
    public function testSidbarMenu()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        self::assertResponseIsSuccessful();
        $crawlerSidebar = $crawler->filter('.main-sidebar')->first();
        $navLinkInfo = $crawlerSidebar->filter('a.nav-link')->extract('href');
        static::assertThat(
            count($navLinkInfo),
            new GreaterThan(1),
            'The sidebar menu contains at least 1 item.'
        );
        foreach ($navLinkInfo as $elementCrawler) {
            $navPageCrawler = $client->request('GET', '/');
            self::assertResponseIsSuccessful();
            static::assertThat(
                $navPageCrawler->filter('.main-sidebar')->count(),
                new GreaterThan(0),
                'The navigation page contains a sidebar.'
            );
        }
    }

    public function testSidebarFilter()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ozg-leistungen');
        self::assertResponseIsSuccessful();
        $crawlerSidebar = $crawler->filter('.main-sidebar')->first();
        $filterLinkCrawler = $crawlerSidebar->filter('a.custom-menu-filter-link');
        foreach ($filterLinkCrawler as $elementCrawler) {

            $urlAttr = $elementCrawler->attributes->getNamedItem('data-url');
            $filterValueAttr = $elementCrawler->attributes->getNamedItem('data-filter-value');
            $urlTemplate = $urlAttr ? $urlAttr->nodeValue : null;
            $filterValue = $filterValueAttr ? $filterValueAttr->nodeValue : null;
            static::assertNotEmpty(
                $urlTemplate,
                'The filter link has a data url.'
            );
            static::assertNotEmpty(
                $filterValue,
                'The filter link has a filter value.'
            );
            $url = str_replace('', $filterValue, $urlTemplate);
            $filterCrawler = $client->request('GET', $url);
            self::assertResponseIsSuccessful();

            static::assertThat(
                $filterCrawler->filter('.sonata-filter-form')->count(),
                new GreaterThan(0),
                'The filter result contains a filter form.'
            );
        }
    }
}