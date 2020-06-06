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
 * Functional test for the controllers defined inside frontend admin controllers.
 *
 * See https://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ ./vendor/bin/phpunit
 */
abstract class AbstractFrontendAdminControllerTestCase extends WebTestCase
{

    abstract protected function getRoutePrefix(): string;

    protected function getRoute(string $view, array $params = []): string
    {
        $route = $this->getRoutePrefix();
        if ($view !== 'list') {
            if (array_key_exists('id', $params)) {
                $route .= '/' . $params['id'];
            }
            return $route . '/' . $view;
        }
        return $route;
    }

    public function testIndex()
    {
        $testViewData = [];
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getRoute('list'));
        self::assertResponseIsSuccessful();
        $crawlerContent = $crawler->filter('.content-wrapper')->first();
        $linkInfo = $crawlerContent->filter('a')->extract('href');
        shuffle($linkInfo);
        $routePattern = '/\/?'.preg_quote($this->getRoutePrefix(), '/').'\/(\d+)\/(\w+)/';
        foreach ($linkInfo as $link) {
            // No links to admin backend in content section in frontend
            $this->assertNotContains('/admin/', $link);
            if (preg_match($routePattern, $link, $matches)) {
                $testViewData[$matches[2]][$matches[1]] = $matches[1];
            }
        }
        $this->assertContains(
            'Volltextsuche',
            $crawler->filter('.sonata-actions')->html(),
            'The full text search field is present.',
            false,
            false
        );
        $this->assertContains(
            'format=xlsx',
            implode(',', $linkInfo),
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
    public function testShow(array $testViewData)
    {
        $testIds = [];
        if (!empty($testViewData['show'])) {
            $testIds = $testViewData['show'];
            shuffle($testIds);
        }
        $maxCount = min(3, count($testIds));
        $count = 0;
        foreach ($testIds as $id)  {
            $route = $this->getRoute('show', ['id' => $id]);
            $client = static::createClient();
            $crawler = $client->request('GET', $route);
            self::assertResponseIsSuccessful();

            $this->assertNotEmpty(
                $crawler->filter('.sonata-ba-view'),
                'The show view has been rendered for ID: ' . $id
            );
            $this->assertNotEmpty(
                $crawler->filter('.sonata-action-element'),
                'The back button exists for ID: ' . $id
            );

            static::assertThat(
                $crawler->filter('.box-title')->count(),
                new GreaterThan(0),
                'At least on box exists with a title for ID: ' . $id
            );
            ++$count;
            if ($count >= $maxCount) {
                break;
            }
        }
    }
}