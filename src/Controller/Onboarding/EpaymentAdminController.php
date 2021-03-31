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


use App\Entity\Onboarding\Epayment;
use App\Service\Onboarding\InjectOnboardingManagerTrait;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EpaymentAdminController
 */
class EpaymentAdminController extends CRUDController
{
    use InjectOnboardingManagerTrait;

    /**
     * @inheritDoc
     */
    protected function preList(Request $request)
    {
        $this->onboardingManager->createItems(Epayment::class);
        return null;
    }
}
