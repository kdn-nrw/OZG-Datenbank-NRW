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
 * @since     2018-01-25
 */

namespace App\Statistics;

use App\Statistics\Model\StatisticsFilterModelInterface;

/**
 * Interface for filterable statistics providers
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2018 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      http://www.brain-appeal.com/
 * @since     2018-01-25
 */
interface FilterableStatisticsProviderInterface
{

    /**
     * Returns the filter model for this provider
     *
     * @return StatisticsFilterModelInterface
     */
    public function getFilterModel();

    /**
     * Returns the class name of the form type
     *
     * @return string
     */
    public function getFormTypeClass();
}