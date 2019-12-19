<?php

namespace App\Form\DataTransformer;

use App\Entity\Base\BaseEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityCollectionToIdArrayTransformer implements DataTransformerInterface
{
    private $entityManager;
    private $entityClass;

    public function __construct(ModelManagerInterface $entityManager, $entityClass)
    {
        $this->entityManager = $entityManager;
        $this->entityClass = $entityClass;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  BaseEntityInterface[]|Collection $entities
     * @return int[]
     */
    public function transform($entities)
    {
        if (null === $entities) {
            return [];
        }
        $entityIdList = [];
        foreach ($entities as $entity) {
            $entityIdList[] = $entity->getId();
        }
        return $entityIdList;
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  int[] $entityIdList
     * @return BaseEntityInterface[]|Collection
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($entityIdList)
    {
        $entities = new ArrayCollection();
        if (empty($entityIdList)) {
            return null;
        }

        foreach ($entityIdList as $entityId) {
            $entity = $this->entityManager
                ->find($this->entityClass, $entityId);

            if (null !== $entity) {
                /** @var BaseEntityInterface $entity */
                $entities->add($entity);
            }
        }
        return $entities;
    }
}