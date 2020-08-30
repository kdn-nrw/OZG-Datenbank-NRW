<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Search;

/**
 * Search finder
 */
class Finder extends AbstractSearchService
{
    public function findMatchingRecordIds(string $model, string $searchTerm)
    {
        $indexRepository = $this->getIndexRepository();
        $context = $this->applicationContextHandler->getApplicationContext();
        return $indexRepository->findMatchingIndexRecords($model, $context, $searchTerm);
    }
}