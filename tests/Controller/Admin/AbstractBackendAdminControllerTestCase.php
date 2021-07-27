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
    protected $hasFeatureFullTextSearchField = true;
    protected $hasFeatureActionShow = true;
    protected $hasFeatureActionDelete = true;

    public function testIndex(): array
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $this->logIn($client);
        $url = $this->getRouteUrl('list');
        $crawler = $client->request('GET', $url);
        self::assertResponseIsSuccessful();
        $crawlerContent = $crawler->filter(self::SELECTOR_CONTENT_SECTION)->first();
        if ($this->hasFeatureFullTextSearchField) {
            self::assertContains(
                'Volltextsuche',
                $crawler->filter('.sonata-actions')->html(),
                'The full text search field is present.',
                false,
                false
            );
        }
        if ($crawlerContent->filter('.sonata-ba-list')->count() > 0) {
            $testViewData = $this->parseLinks($crawlerContent, '');
            $exportLinks = $testViewData['export'] ?? [];
            self::assertContains(
                'format=xlsx',
                implode(',', $exportLinks),
                'The excel export link is present.',
                false,
                false
            );
            self::assertContains(
                'custom-search-form',
                $crawler->filter('.main-sidebar')->html(),
                'The global search form is present.',
                false,
                false
            );

            self::assertThat(
                $crawler->filter('.sonata-ba-list-field')->count(),
                new GreaterThan(1),
                'The list view contains at least 1 data row.'
            );
        } else {
            static::assertNotEmpty(
                $crawlerContent->filter('.content')->first()->filter('.sonata-action-element'),
                'The list view has no rows and does not contain the new button'
            );
            $testViewData = [];
        }

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

                    self::assertNotEmpty(
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
    public function testShow(array $testViewData): void
    {
        if ($this->hasFeatureActionShow) {
            $this->checkItemView($testViewData, 'show');
        } else {
            self::assertEmpty(
                $testViewData['show'] ?? null,
                'The show view is disabled but the list view contains links to the show view'
            );
        }
    }

    /**
     * @depends testIndex
     * @param array $testViewData
     */
    public function testEdit(array $testViewData): void
    {
        $this->checkItemView($testViewData, 'edit');
    }

    /**
     * @depends testIndex
     * @param array $testViewData
     */
    public function testDelete(array $testViewData): void
    {
        if ($this->hasFeatureActionShow) {
            $this->checkItemView($testViewData, 'delete');
        } else {
            self::assertEmpty(
                $testViewData['delete'] ?? null,
                'The delete view is disabled but the list view contains links to the delete view'
            );
        }
    }

    /**
     * @param array $testViewData
     * @param string $view
     */
    protected function checkItemView(array $testViewData, string $view): void
    {
        $testIds = [];
        if (!empty($testViewData[$view])) {
            $testIds = $testViewData[$view];
            shuffle($testIds);
        }
        if (empty($testIds)) {
            self::markTestSkipped(sprintf('The test data contain no link to the %s view', $view));
        } else {
            $maxCount = min(3, count($testIds));
            $count = 0;
            foreach ($testIds as $id) {
                $route = $this->getRouteUrl($view, ['id' => $id]);
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
                    case 'delete':
                        $this->runDeleteAssertions($crawler, $id);
                        break;
                }
                ++$count;
                if ($count >= $maxCount) {
                    break;
                }
            }
        }
    }

    protected function runShowAssertions(Crawler $crawler, int $id)
    {
        $crawlerContent = $crawler->filter(self::SELECTOR_CONTENT_SECTION)->first();
        self::assertNotEmpty(
            $crawlerContent->filter('.sonata-ba-view'),
            'The show view has been rendered for ID: ' . $id
        );
        self::assertNotEmpty(
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
        self::assertNotEmpty(
            $crawlerContent->filter('.sonata-ba-form'),
            'The edit view has been rendered for ID: ' . $id
        );
        self::assertNotEmpty(
            $crawlerContent->filter('.sonata-ba-form-actions'),
            'The edit button exists for ID: ' . $id
        );

        static::assertThat(
            $crawlerContent->filter('.form-control')->count(),
            new GreaterThan(0),
            'At least one form control exists for ID: ' . $id
        );
    }

    protected function runDeleteAssertions(Crawler $crawler, int $id)
    {
        $crawlerContent = $crawler->filter(self::SELECTOR_CONTENT_SECTION)->first();
        $referenceContent = $crawlerContent->filter('.object-references-info')->first();
        static::assertNotEmpty(
            $referenceContent,
            'The reference section does not exist for ID: ' . $id
        );
        if ($referenceContent->filter('.label-danger')->count() > 0) {
            static::assertNotEmpty(
                $crawlerContent->filter('.alert-references-delete'),
                'The delete alert exists for ID: ' . $id
            );
        } else {
            static::assertNotEmpty(
                $crawlerContent->filter('.btn-danger'),
                'The delete button exists for ID: ' . $id
            );
        }
    }
}