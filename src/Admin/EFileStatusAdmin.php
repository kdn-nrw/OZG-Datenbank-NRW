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


class EFileStatusAdmin extends StatusAdmin
{

    /**
     * Hook to run after initialization.
     */
    protected function configure(): void
    {
        parent::configure();
        $this->classnameLabel = 'efileStatus';
    }
}
