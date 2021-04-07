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

namespace App\Service\Onboarding;

trait InjectInquiryManagerTrait
{
    /**
     * @var InquiryManager
     */
    protected $inquiryManager;

    /**
     * @required
     * @param InquiryManager $inquiryManager
     */
    public function injectInquiryManager(InquiryManager $inquiryManager): void
    {
        $this->inquiryManager = $inquiryManager;
    }
}