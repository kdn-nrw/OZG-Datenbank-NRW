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

namespace App\Entity\Onboarding;

use App\Entity\Configuration\CustomValue;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class onboarding custom value
 *
 * @ORM\Entity
 */
class OnboardingCustomValue extends CustomValue
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\AbstractOnboardingEntity", inversedBy="customValues")
     * @ORM\JoinColumn(name="onboarding_id", referencedColumnName="id", nullable=true)
     * @var AbstractOnboardingEntity|null
     */
    protected $onboarding;

    /**
     * @return AbstractOnboardingEntity|null
     */
    public function getOnboarding()
    {
        return $this->onboarding;
    }

    /**
     * @param AbstractOnboardingEntity|null $onboarding
     */
    public function setOnboarding($onboarding): void
    {
        $this->onboarding = $onboarding;
    }

}
