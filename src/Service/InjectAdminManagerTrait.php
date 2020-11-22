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

namespace App\Service;

trait InjectAdminManagerTrait
{

    /**
     * @var AdminManager
     */
    protected $adminManager;

    /**
     * @required
     * @param AdminManager $adminManager
     */
    public function injectAdminManager(AdminManager $adminManager): void
    {
        $this->adminManager = $adminManager;
    }

}