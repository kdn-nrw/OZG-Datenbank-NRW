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

namespace App\Admin\Frontend;


class ServiceAccountAdmin extends AbstractOnboardingAdmin
{
    protected $baseRoutePattern = 'onboarding/servicekonto';

    protected function getRoutePrefix(): string
    {
        return 'frontend_app_onboarding_service_account';
    }
}
