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

namespace App\Service;

trait InjectAuditManagerTrait
{

    /**
     * @var AuditManager
     */
    protected $auditManager;

    /**
     * @required
     * @param AuditManager $auditManager
     */
    public function injectAuditManager(AuditManager $auditManager): void
    {
        $this->auditManager = $auditManager;
    }

}