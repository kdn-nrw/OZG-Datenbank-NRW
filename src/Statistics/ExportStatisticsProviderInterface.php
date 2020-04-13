<?php
/**
 * Mindbase 3
 *
 * PHP version 5.6
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2018 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      http://www.brain-appeal.com/
 * @since     2018-01-26
 */

namespace App\Statistics;

/**
 * Interface for export statistics providers
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2018 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      http://www.brain-appeal.com/
 * @since     2018-01-26
 */
interface ExportStatisticsProviderInterface
{

    /**
     * Returns the provider export data
     *
     * @return array
     */
    public function getExportData();

    /**
     * Returns the provider export options
     *
     * @return array
     */
    public function getExportOptions();
}