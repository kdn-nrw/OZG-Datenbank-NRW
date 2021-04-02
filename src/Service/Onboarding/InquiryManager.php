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
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Onboarding\Inquiry;
use Symfony\Component\PropertyAccess\PropertyAccess;

class InquiryManager
{
    use InjectManagerRegistryTrait;

    /**
     * Create commune info items for all communes
     *
     * @param Inquiry $inquiry
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveInquiry(Inquiry $inquiry, BaseEntityInterface $entity): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if ($propertyAccessor->isReadable($entity, 'messageCount')
            && $propertyAccessor->isWritable($entity, 'messageCount')) {
            $messageCount = (int)$propertyAccessor->getValue($entity, 'messageCount') + 1;
            $propertyAccessor->setValue($entity, 'messageCount', $messageCount);
        }
        $em = $this->getEntityManager();
        $em->persist($inquiry);
        $em->flush();
    }

    /**
     * @param BaseEntityInterface $entity
     * @return int|mixed|string
     */
    public function findEntityInquiries(BaseEntityInterface $entity)
    {
        $repository = $this->getEntityManager()->getRepository(Inquiry::class);
        $queryBuilder = $repository->createQueryBuilder('i');
        $queryBuilder
            ->where('i.referenceId = :referenceId')
            ->andWhere('i.referenceSource = :referenceSource')
            ->setParameters([
                'referenceSource' => get_class($entity),
                'referenceId' => $entity->getId()
            ]);
        $queryBuilder->orderBy('i.createdAt', 'DESC');
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}