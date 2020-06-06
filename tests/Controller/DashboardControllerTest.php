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
 * Functional test for the controllers defined inside SolutionController.
 */
class DashboardControllerTest extends WebTestCase implements FrontendTestInterface
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $crawler = $client->request('GET', '/');
        self::assertResponseIsSuccessful();

        $crawlerContent = $crawler->filter(self::SELECTOR_CONTENT_SECTION)->first();
        static::assertThat(
            $crawlerContent->filter('.box-page-content')->count(),
            new GreaterThan(0),
            'The view contains at least 1 page content box.'
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