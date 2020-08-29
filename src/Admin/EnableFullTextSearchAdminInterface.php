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

namespace App\Admin;


/**
 * Interface EnableFullTextSearchAdminInterface
 */
interface EnableFullTextSearchAdminInterface
{
    /**
     * Returns the template for this admin used for creating the search index
     * @return string
     */
    public function getSearchIndexingTemplate(): string;
}
