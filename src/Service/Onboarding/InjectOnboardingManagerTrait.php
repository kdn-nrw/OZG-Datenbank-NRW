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

namespace App\Service\Onboarding;

trait InjectOnboardingManagerTrait
{
    /**
     * @var OnboardingManager
     */
    protected $onboardingManager;

    /**
     * @required
     * @param OnboardingManager $onboardingManager
     */
    public function injectOnboardingManager(OnboardingManager $onboardingManager): void
    {
        $this->onboardingManager = $onboardingManager;
    }
}