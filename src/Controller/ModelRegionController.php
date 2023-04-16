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


use App\Admin\Frontend\ModelRegionAdmin;

/**
 * Class ModelRegionProjectController
 */
class ModelRegionController extends AbstractFrontendCRUDController
{
    /**
     * @inheritDoc
     */
    protected function getDefaultRouteName(): string
    {
        return 'frontend_app_modelregion_list';
    }

    /**
     * @inheritDoc
     */
    protected function getAdminClassName(): string
    {
        return ModelRegionAdmin::class;
    }
}
