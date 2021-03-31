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

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Onboarding\Inquiry;

class InquiryManager
{
    use InjectManagerRegistryTrait;

    /**
     * Create commune info items for all communes
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function saveInquiry(Inquiry $inquiry): void
    {
        $em = $this->getEntityManager();
        $em->persist($inquiry);
        $em->flush();
    }
}