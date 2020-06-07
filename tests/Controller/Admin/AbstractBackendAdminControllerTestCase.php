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

use PHPUnit\Framework\Constraint\GreaterThan;
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
abstract class AbstractBackendAdminControllerTestCase extends AbstractBackendTestCase
{

    public function testIndex(): array
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $this->logIn($client);
        $url = $this->getRouteUrl('list');
        $crawler = $client->request('GET', $url);
        self::assertResponseIsSuccessful();
        $crawlerContent = $crawler->filter(self::SELECTOR_CONTENT_SECTION)->first();
        $testViewData = $this->parseLinks($crawlerContent, '');
        $this->assertContains(
            'Volltextsuche',
            $crawler->filter('.sonata-actions')->html(),
            'The full text search field is present.',
            false,
            false
        );
        $exportLinks = $testViewData['export'] ?? [];
        $this->assertContains(
            'format=xlsx',
            implode(',', $exportLinks),
            'The excel export link is present.',
            false,
            false
        );
        $this->assertContains(
            'custom-search-form',
            $crawler->filter('.main-sidebar')->html(),
            'The global search form is present.',
            false,
            false
        );

        static::assertThat(
            $crawler->filter('.sonata-ba-list-field')->count(),
            new GreaterThan(10),
            'The list view contains at least 1 data row.'
        );

        return $testViewData;
    }

    /**
     * @depends testIndex
     * @param array $testViewData
     */
    public function testPager(array $testViewData)
    {
        if (!empty($testViewData['list'])) {
            $route = $this->getRouteUrl('list');
            $listUrls = $testViewData['list'];
            $pageLinksChecked = 0;
            foreach ($listUrls as $query) {
                parse_str($query, $params);
                if (!empty($params['filter']['_page'])) {
                    $client = static::createClient();
                    $client->catchExceptions(false);
                    $this->logIn($client);
                    $crawler = $client->request('GET', $route, $params);
                    self::assertResponseIsSuccessful();

                    $this->assertNotEmpty(
                        $crawler->filter(self::SELECTOR_CONTENT_SECTION),
                        'The show view has been rendered for query: ' . $query
                    );
                    ++$pageLinksChecked;
                    if ($pageLinksChecked > 2) {
                        break;
                    }
                }
            }
        }
    }

    /**
     * @depends testIndex
     * @param array $testViewData
     */
    public function testShow(array $testViewData)
    {
        $this->checkItemView($testViewData, 'show');
    }

    /**
     * @depends testIndex
     * @param array $testViewData
     */
    public function testEdit(array $testViewData)
    {
        $this->checkItemView($testViewData, 'edit');
    }

    /**
     * @param array $testViewData
     * @param string $view
     */
    protected function checkItemView(array $testViewData, string $view)
    {
        $testIds = [];
        if (!empty($testViewData[$view])) {
            $testIds = $testViewData[$view];
            shuffle($testIds);
        }
        $maxCount = min(3, count($testIds));
        $count = 0;
        foreach ($testIds as $id) {
            $route = $this->getRouteUrl($view, ['id' => $id]);
            echo '$route: '.print_r($route, true)."\n";
            $client = static::createClient();
            $client->catchExceptions(false);
            $this->logIn($client);
            $crawler = $client->request('GET', $route);
            self::assertResponseIsSuccessful();
            switch ($view) {
                case 'show':
                    $this->runShowAssertions($crawler, $id);
                    break;
                case 'edit':
                    $this->runEditAssertions($crawler, $id);
                    break;
            }
            ++$count;
            if ($count >= $maxCount) {
                break;
            }
        }
    }

    protected function runShowAssertions(Crawler $crawler, int $id)
    {
        $crawlerContent = $crawler->filter(self::SELECTOR_CONTENT_SECTION)->first();
        $this->assertNotEmpty(
            $crawlerContent->filter('.sonata-ba-view'),
            'The show view has been rendered for ID: ' . $id
        );
        $this->assertNotEmpty(
            $crawlerContent->filter('.sonata-action-element'),
            'The back button exists for ID: ' . $id
        );

        static::assertThat(
            $crawlerContent->filter('.box-title')->count(),
            new GreaterThan(0),
            'At least on box exists with a title for ID: ' . $id
        );
    }

    protected function runEditAssertions(Crawler $crawler, int $id)
    {
        $crawlerContent = $crawler->filter(self::SELECTOR_CONTENT_SECTION)->first();
        $this->assertNotEmpty(
            $crawlerContent->filter('.sonata-ba-form'),
            'The edit view has been rendered for ID: ' . $id
        );
        $this->assertNotEmpty(
            $crawlerContent->filter('.sonata-ba-form-actions'),
            'The edit button exists for ID: ' . $id
        );

        static::assertThat(
            $crawlerContent->filter('.form-control')->count(),
            new GreaterThan(0),
            'At least on form control exists for ID: ' . $id
        );
    }
}