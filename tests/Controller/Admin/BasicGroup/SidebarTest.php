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

namespace App\Tests\Controller\Admin\BasicGroup;

use App\Tests\Controller\Admin\AbstractBackendTestCase;
use PHPUnit\Framework\Constraint\GreaterThan;

/**
 * Functional test for the frontend sidebar.
 */
class SidebarTest extends AbstractBackendTestCase
{

    protected function getRoutePrefix(): string
    {
        return 'dashboard';
    }

    protected function getContextPrefix(): string
    {
        return self::BACKEND_URL_PREFIX;
    }

    public function testGlobalSearch()
    {
        $client = self::createClient();
        $client->catchExceptions(false);
        $this->logIn($client);
        $url = $this->getRouteUrl('index');
        $crawler = $client->request('GET', $url);
        self::assertResponseIsSuccessful();
        $crawlerSidebar = $crawler->filter('.main-sidebar')->first();

        $buttonCrawlerNode = $crawlerSidebar->filter('.custom-search-form button');

        // you can also pass an array of field values that overrides the default ones
        $form = $buttonCrawlerNode->form([
            'q' => 'Baugenehmigung',
        ]);
        $crawlerResult = $client->submit($form);

        $crawlerContent = $crawlerResult->filter(self::SELECTOR_CONTENT_SECTION)->first();
        self::assertThat(
            $crawlerContent->filter('.search-box-item')->count(),
            new GreaterThan(1),
            'The view contains at least 1 search result box.'
        );
    }

    public function testSidbarMenu()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $this->logIn($client);
        $url = $this->getRouteUrl('index');
        $crawler = $client->request('GET', $url);
        self::assertResponseIsSuccessful();
        $crawlerSidebar = $crawler->filter('.main-sidebar')->first();
        $navLinkInfo = $crawlerSidebar->filter('.sidebar-menu a')->extract(['href']);

        self::assertThat(
            count($navLinkInfo),
            new GreaterThan(0),
            'The sidebar menu contains at least 1 item.'
        );
        // Test all link in sidebar nav
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
}