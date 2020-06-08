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

use PHPUnit\Framework\Constraint\IsEmpty;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Functional test for the controllers defined inside SolutionController.
 */
class SolutionAdminTest extends AbstractBackendAdminControllerTestCase
{

    protected function getRoutePrefix(): string
    {
        return 'solution';
    }
/*
    protected function runShowAssertions(Crawler $crawler, int $id)
    {
        parent::runShowAssertions($crawler, $id);

        static::assertThat(
            $crawler->filter('.service-is-missing')->count(),
            new IsEmpty(),
            'No solution service is missing ID: ' . $id
        );
    }*/
}