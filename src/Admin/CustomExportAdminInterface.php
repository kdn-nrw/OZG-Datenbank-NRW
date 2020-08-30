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

namespace App\Admin;


use App\Model\ExportSettings;

/**
 * Class CustomExportAdminInterface
 */
interface CustomExportAdminInterface
{
    /**
     * Returns the export settings for this admin
     *
     * @return ExportSettings
     */
    public function getExportSettings(): ExportSettings;
}
