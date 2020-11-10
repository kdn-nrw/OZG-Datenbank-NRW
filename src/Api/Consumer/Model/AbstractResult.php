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

namespace App\Api\Consumer\Model;

use App\Import\Model\AbstractImportModel;

abstract class AbstractResult extends AbstractImportModel
{
    /**
     * Returns the import key data
     *
     * @return array|null
     */
    public function getImportKeyData(): ?array
    {
        return null;
    }
}