<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Base;


/**
 * Class AuditedEntityAdminInterface
 */
interface AuditedEntityAdminInterface
{

    /**
     * Returns the audit state
     *
     * @return bool
     */
    public function isEntityAuditEnabled(): bool;

    /**
     * Sets the entity audit state
     *
     * @param bool $entityAuditEnabled
     */
    public function setEntityAuditEnabled(bool $entityAuditEnabled): void;
}
