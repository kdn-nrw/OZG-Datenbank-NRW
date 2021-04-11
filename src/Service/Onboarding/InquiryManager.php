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
use App\Entity\Onboarding\AbstractOnboardingEntity;
use App\Entity\Onboarding\Inquiry;
use App\Entity\User;
use App\Form\Type\InquiryType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class InquiryManager
{
    use InjectManagerRegistryTrait;
    use InjectSecurityTrait;

    /**
     * Internal cache for referenced objects
     * @var array
     */
    private $referenceObjectCache = [];

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

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
        if ($entity instanceof Inquiry) {
            $referencedEntity = $this->getReferencedObject($entity->getReferenceSource(), $entity->getReferenceId());
            if (null !== $createdBy = $entity->getCreatedBy()) {
                $inquiry->setUser($createdBy);
            }
            $inquiry->setReferenceSource($entity->getReferenceSource());
            $inquiry->setReferenceId($entity->getReferenceId());
            $entity->addAnswer($inquiry);
        } else {
            $referencedEntity = $entity;
        }
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if (null !== $referencedEntity && $propertyAccessor->isReadable($referencedEntity, 'messageCount')
            && $propertyAccessor->isWritable($referencedEntity, 'messageCount')) {
            $messageCount = (int)$propertyAccessor->getValue($referencedEntity, 'messageCount') + 1;
            $propertyAccessor->setValue($referencedEntity, 'messageCount', $messageCount);
        }
        $em = $this->getEntityManager();
        $em->persist($inquiry);
        $em->flush();
    }

    /**
     * Mark the given list of inquiries as read (by the current user)
     * @param Inquiry[]|array|iterable $inquiries
     * @param bool $onlyForRecipient Only mark as read if the current user is the recipient (don't mark general messages as read)
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function markInquiryListAsRead($inquiries, $onlyForRecipient = true): void
    {
        $currentUser = $this->security->getUser();
        $changeCount = 0;
        foreach ($inquiries as $inquiry) {
            /** @var Inquiry $inquiry */
            if (!$inquiry->isRead()
                && $inquiry->getCreatedBy() !== $currentUser
                // Mark as read if inquiry is directed to current user or if inquiry is not user-specific
                && (null === $inquiry->getUser() || $inquiry->getUser() === $currentUser)) {
                $inquiry->setIsRead(true);
                $inquiry->setReadAt(date_create());
                $inquiry->setReadBy($currentUser);
                ++$changeCount;
            }
            if ($inquiry->getAnswers()->count() > 0) {
                $this->markInquiryListAsRead($inquiry->getAnswers(), $onlyForRecipient);
            }
        }
        if ($changeCount > 0) {
            $em = $this->getEntityManager();
            $em->flush();
        }
    }

    /**
     * @param BaseEntityInterface $entity
     * @param bool $loadAnswers Toggle loading of answers in main list
     * @return Inquiry[]|array|mixed
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function findEntityInquiries(BaseEntityInterface $entity, $loadAnswers = false)
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository(Inquiry::class);
        $queryBuilder = $repository->createQueryBuilder('i');
        $queryBuilder
            ->where('i.referenceId = :referenceId')
            ->andWhere('i.referenceSource = :referenceSource')
            ->setParameters([
                'referenceSource' => get_class($entity),
                'referenceId' => $entity->getId()
            ]);
        if (!$loadAnswers) {
            $queryBuilder->andWhere('i.parent IS NULL');
        }
        $queryBuilder->orderBy('i.createdAt', 'DESC');
        $query = $queryBuilder->getQuery();
        $inquiries = $query->getResult();
        if ($entity instanceof AbstractOnboardingEntity && $entity->getMessageCount() !== count($inquiries)) {
            $entity->setMessageCount(count($inquiries));
            $em->flush();
        }
        return $inquiries;
    }

    /**
     * @param UserInterface $user
     * @param bool $onlyNew
     * @return Inquiry[]|array|mixed
     */
    public function findUserInquiries(UserInterface $user, $onlyNew = true)
    {
        /** @var User $user */
        $em = $this->getEntityManager();
        $repository = $em->getRepository(Inquiry::class);
        $queryBuilder = $repository->createQueryBuilder('i');
        $queryBuilder
            ->orWhere('i.user = :user')
            ->setParameter('user', $user->getId());
        if ($onlyNew) {
            $queryBuilder->andWhere('i.isRead = :isRead')
                ->setParameter('isRead', false);
        }
        $referenceList = [];
        $communes = $user->getCommunes();
        if ($communes->count() > 0) {
            foreach ($communes as $commune) {
                $referenceList[get_class($commune)][] = $commune->getId();
            }
        }
        $offset = 0;
        foreach ($referenceList as $referenceSource => $referenceIdList) {
            $prmSource = 'referenceSource' . $offset;
            $prmId = 'referenceIdList' . $offset;
            $queryBuilder->orWhere(sprintf('i.referenceSource = :%s AND i.referenceId IN (:%s)', $prmSource, $prmId))
                ->setParameter($prmSource, $referenceSource)
                ->setParameter($prmId, $referenceIdList);
            ++$offset;
        }
        $queryBuilder->orderBy('i.createdAt', 'DESC');
        $query = $queryBuilder->getQuery();
        $inquiries = $query->getResult();
        $messages = [];
        foreach ($inquiries as $inquiry) {
            /** @var Inquiry $inquiry */
            $referencedObject = $this->getReferencedObject($inquiry->getReferenceSource(), $inquiry->getReferenceId());
            $createdBy = $inquiry->getCreatedBy();
            if (null !== $referencedObject && $createdBy !== $user) {
                /** @var User $createdBy */
                $messages[] = [
                    'id' => $inquiry->getId(),
                    'text' => $inquiry->getDescription(),
                    'createdByName' => null !== $createdBy ? $createdBy->getFullname() : '',
                    'referencedObject' => $referencedObject,
                    'referenceSource' => $inquiry->getReferenceSource(),
                ];
            }
        }
        return $messages;
    }

    /**
     * Returns the message counts for the given entity
     *
     * @param BaseEntityInterface $entity
     *
     * @param UserInterface $user
     * @return array|int[] The number of messages (new, answer, total)
     */
    public function countEntityInquiries(BaseEntityInterface $entity, UserInterface $user): array
    {
        $repository = $this->getEntityManager()->getRepository(Inquiry::class);
        $queryBuilder = $repository->createQueryBuilder('i');
        $queryBuilder
            ->select('i.isRead', 'IDENTITY(i.parent) AS parentId', 'IDENTITY(i.user) AS userId', 'IDENTITY(i.createdBy) as createdById')
            ->where('i.referenceId = :referenceId')
            ->andWhere('i.referenceSource = :referenceSource')
            ->andWhere('i.hidden = :hidden')
            ->setParameters([
                'referenceSource' => get_class($entity),
                'referenceId' => $entity->getId(),
                'hidden' => false
            ]);
        $query = $queryBuilder->getQuery();
        $result = $query->getScalarResult();
        $messageCountInfo = [
            'new' => 0,
            'answers' => 0,
            'total' => 0,
            'isRecipient' => 0,
            'isSender' => 0,
        ];
        $userId = null !== $user ? $user->getId() : null;
        foreach ($result as $row) {
            if ($row['parentId']) {
                ++$messageCountInfo['answers'];
            }
            if ((int)$row['userId'] === $userId) {
                ++$messageCountInfo['isRecipient'];
            }
            if ((int)$row['createdById'] === $userId) {
                ++$messageCountInfo['isSender'];
            } elseif (!$row['isRead']) {
                ++$messageCountInfo['new'];
            }
        }
        return $messageCountInfo;
    }

    /**
     * Returns an entity referenced by an inquiry
     *
     * @param string $referenceSource A class name
     * @param int|null $referenceId The referenced entity id
     * @return object|null
     */
    public function getReferencedObject(string $referenceSource, ?int $referenceId): ?BaseEntityInterface
    {
        if (empty($referenceSource) || (int)$referenceId <= 0) {
            return null;
        }
        $key = $referenceSource . '_' . (int)$referenceId;
        if (!array_key_exists($key, $this->referenceObjectCache)) {
            $this->referenceObjectCache[$key] = null;
            $em = $this->getEntityManager();
            try {
                if (class_exists($referenceSource) && strpos($referenceSource, 'Entity') !== false) {
                    $this->referenceObjectCache[$key] = $em->find($referenceSource, $referenceId);
                }
            } catch (OptimisticLockException $e) {
            } catch (TransactionRequiredException $e) {
            } catch (ORMException $e) {
            }
        }
        return $this->referenceObjectCache[$key];
    }

    /**
     * Pre fill the inquiry based on the given entity
     *
     * @param Inquiry $inquiry
     * @param BaseEntityInterface $entity
     * @return bool
     */
    protected function prefillInquiry(Inquiry $inquiry, BaseEntityInterface $entity)
    {
        if ($entity instanceof Inquiry) {
            $inquiry->setReferenceSource($entity->getReferenceSource());
            $inquiry->setReferenceId($entity->getReferenceId());
        } else {
            $inquiry->setReferenceId($entity->getId());
            $inquiry->setReferenceSource(get_class($entity));
        }
        $enableUser = true;
        // Answer to previous inquiry
        if ($entity instanceof Inquiry) {
            $inquiry->setUser($entity->getCreatedBy());
        }/* elseif ($entity instanceof BlameableInterface
            && (null !== $modifiedBy = $entity->getModifiedBy())
            && $modifiedBy !== $this->security->getUser()
            && $modifiedBy !== $entity->getCreatedBy()) {
            $inquiry->setUser($modifiedBy);
            $enableUser = true;
        }*/
        return $enableUser;
    }

    /**
     * Creates an inquiry form
     *
     * @param Inquiry $inquiry
     * @param BaseEntityInterface $entity
     * @param string $formAction
     * @return FormInterface
     */
    public function createFormForEntity(Inquiry $inquiry, BaseEntityInterface $entity, string $formAction): FormInterface
    {
        $enableUser = $this->prefillInquiry($inquiry, $entity);
        $form = $this->formFactory->create(InquiryType::class, $inquiry, [
            'action' => $formAction,
            'enable_user' => $enableUser,
        ]);
        return $form;
    }
}