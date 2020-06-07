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

/**
 * Functional test for the controllers defined inside SolutionController.
 */
class DashboardControllerTest extends AbstractBackendTestCase
{

    protected function getRoutePrefix(): string
    {
        return 'dashboard';
    }

    protected function getContextPrefix(): string
    {
        return self::BACKEND_URL_PREFIX;
    }

    public function testIndex()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $this->logIn($client);
        $url = $this->getRouteUrl('index');
        $crawler = $client->request('GET', $url);
        self::assertResponseIsSuccessful(
            'The url request ' . self::BACKEND_URL_PREFIX . ' must be successful'
        );

        $crawlerContent = $crawler->filter(self::SELECTOR_CONTENT_SECTION)->first();
        static::assertThat(
            $crawler->filter('.dropdown-user')->count(),
            new GreaterThan(0),
            'The view must contain the user profile dropdown.'
        );
        static::assertThat(
            $crawlerContent->filter('.small-box')->count(),
            new GreaterThan(1),
            'The view contains at least 2 page statistics boxes.'
        );
        $chartPlaceholderCrawler = $crawlerContent->filter('.mb-chart-container');
        foreach ($chartPlaceholderCrawler as $elementCrawler) {
            $urlAttr = $elementCrawler->attributes->getNamedItem('data-url');
            $url = $urlAttr ? $urlAttr->nodeValue : null;
            static::assertNotEmpty(
                $url,
                'The chart placeholder container has a data url.'
            );
            $client->xmlHttpRequest('GET', $url);
            $response = $client->getResponse();
            $this->assertSame(200, $response->getStatusCode());
            $responseData = json_decode($response->getContent(), true);
            $this->assertSame('chart', $responseData['type']);
        }
    }
}