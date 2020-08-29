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

namespace App\Datagrid;


use Sonata\AdminBundle\Datagrid\Datagrid;

/**
 * Interface FulltextSearchDatagridInterface
 */
interface FulltextSearchDatagridInterface
{
    /**
     * @see \Sonata\AdminBundle\Datagrid\Datagrid::buildPager
     * @var int
     */
    public const DEFAULT_MAX_RESULTS_PER_PAGE = 25;

    public function setCustomOrderRecordIdList(array $recordIdList);
}