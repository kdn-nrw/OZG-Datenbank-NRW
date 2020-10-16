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

namespace App\Controller;


use App\Admin\Frontend\CommuneAdmin;

/**
 * Class CommuneController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-10-15
 */
class CommuneController extends AbstractFrontendCRUDController
{
    /**
     * @inheritDoc
     */
    protected function getDefaultRouteName(): string
    {
        return 'frontend_app_commune_list';
    }

    /**
     * @inheritDoc
     */
    protected function getAdminClassName(): string
    {
        return CommuneAdmin::class;
    }
}
