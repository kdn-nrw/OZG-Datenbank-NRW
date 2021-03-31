<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Onboarding;


use App\Admin\Frontend\EpaymentAdmin;
use App\Controller\AbstractEditableFrontendController;

/**
 * Class EpaymentController
 */
class EpaymentController extends AbstractEditableFrontendController
{
    /**
     * @inheritDoc
     */
    protected function getDefaultRouteName(): string
    {
        return 'frontend_app_onboarding_epayment_list';
    }

    /**
     * @inheritDoc
     */
    protected function getAdminClassName(): string
    {
        return EpaymentAdmin::class;
    }
}
