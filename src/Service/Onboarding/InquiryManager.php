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
use App\DependencyInjection\InjectionTraits\InjectSecurityTrait;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Onboarding\Inquiry;
use Symfony\Component\PropertyAccess\PropertyAccess;

class InquiryManager
{
    use InjectManagerRegistryTrait;
    use InjectSecurityTrait;

    /**
     * Create commune info items for all communes
     *
     * @param Inquiry $inquiry
     * @param BaseEntityInterface|null $entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveInquiry(Inquiry $inquiry, ?BaseEntityInterface $entity): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if (null !== $entity && $propertyAccessor->isReadable($entity, 'messageCount')
            && $propertyAccessor->isWritable($entity, 'messageCount')) {
            $messageCount = (int)$propertyAccessor->getValue($entity, 'messageCount') + 1;
            $propertyAccessor->setValue($entity, 'messageCount', $messageCount);
        }
        $em = $this->getEntityManager();
        $em->persist($inquiry);
        $em->flush();
    }

    /**
     * Mark the given list of inquiries as read (by the current user)
     * @param Inquiry[]|array|iterable $inquiries
     */
    public function markInquiryListAsRead($inquiries): void
    {
        foreach ($inquiries as $inquiry) {
            /** @var Inquiry $inquiry */
            if (!$inquiry->isRead()) {
                $inquiry->setIsRead(true);
                $inquiry->setReadAt(date_create());
                $inquiry->setReadBy($this->security->getUser());
            }
        }
        $em = $this->getEntityManager();
        $em->flush();
    }

    /**
     * @param BaseEntityInterface $entity
     * @return Inquiry[]|array|mixed
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

    /**
     * @param BaseEntityInterface $entity
     * @param bool $onlyNew Count only new messages
     * @return int The number of messages
     */
    public function countEntityInquiries(BaseEntityInterface $entity, bool $onlyNew = true): int
    {
        $repository = $this->getEntityManager()->getRepository(Inquiry::class);
        $queryBuilder = $repository->createQueryBuilder('i');
        $queryBuilder
            ->select('COUNT(i.id) AS messageCount')
            ->where('i.referenceId = :referenceId')
            ->andWhere('i.referenceSource = :referenceSource')
            ->setParameters([
                'referenceSource' => get_class($entity),
                'referenceId' => $entity->getId()
            ]);
        if ($onlyNew) {
            $queryBuilder
                ->andWhere('i.isRead = :isRead')
                ->setParameter('isRead', false);
        }
        $query = $queryBuilder->getQuery();
        return (int)$query->getSingleScalarResult();
    }
}