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

namespace App\Tests\Controller\Admin\ImplementationGroup;

use App\Tests\Controller\Admin\AbstractBackendAdminControllerTestCase;

/**
 * Functional test for the controllers defined inside ImplementationStatusAdmin
 */
class ConfidenceLevelAdminTest extends AbstractBackendAdminControllerTestCase
{
    protected $hasFeatureFullTextSearchField = false;

    protected function getRoutePrefix(): string
    {
        return 'app/confidencelevel';
    }
}