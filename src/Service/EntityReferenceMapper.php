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

namespace App\Service;

use App\Entity\Base\BaseEntityInterface;
use App\Model\EntityReferenceMap;
use App\Model\EntityReferenceProperty;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EntityReferenceMapper
{

    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry|ManagerRegistry
     */
    protected $registry;

    /**
     * Mapping of entity reference meta data
     * @var array
     */
    private $entityReferenceMeta = [];

    /**
     * @required
     * @param ManagerRegistry $registry
     */
    public function injectManagerRegistry(ManagerRegistry $registry): void
    {
        $this->registry = $registry;
    }

    /**
     * @param EntityReferenceMap $entityReferenceMap
     * @param string $action
     */
    private function addAdditionalEntityReferenceReferences(EntityReferenceMap $entityReferenceMap, string $action): void
    {
        $managers = $this->registry->getManagers();
        foreach ($managers as $manager) {
            $allMetaData = $manager->getMetadataFactory()->getAllMetadata();
            foreach ($allMetaData as $metaData) {
                $associationMappings = $metaData->getAssociationMappings();
                foreach ($associationMappings as $property => $mapping) {
                    if ($mapping['isOwningSide']
                        && $mapping['inversedBy'] === null
                        && $mapping['targetEntity'] === $entityReferenceMap->getEntityClass()) {
                        $entityReferenceProperty = new EntityReferenceProperty($property);
                        $entityReferenceProperty->setMapping($mapping);
                        $entityReferenceProperty->setIsMappedOneSided(true);
                        $associationAction = $entityReferenceProperty->getActionBasedOnMapping($action);
                        $targetEntityReferenceMap = $this->getEntityClassReferenceMeta($mapping['sourceEntity'], $associationAction);
                        $entityReferenceProperty->setTargetEntityReferenceMap($targetEntityReferenceMap);
                        $entityReferenceMap->addPropertyReferences($property, $entityReferenceProperty);
                    }
                }
            }
        }
    }


    /**
     * @param string $entityClass
     * @param string $action
     * @return EntityReferenceMap
     */
    private function getEntityClassReferenceMeta(string $entityClass, string $action): EntityReferenceMap
    {
        if (isset($this->entityReferenceMeta[$entityClass])) {
            return $this->entityReferenceMeta[$entityClass];
        }
        $entityReferenceMap = new EntityReferenceMap($entityClass);
        $this->entityReferenceMeta[$entityClass] = $entityReferenceMap;
        $em = $this->registry->getManager();
        $metaData = $em->getMetadataFactory()->getMetadataFor($entityClass);
        $associationMappings = $metaData->getAssociationMappings();
        foreach ($associationMappings as $property => $mapping) {
            $entityReferenceProperty = new EntityReferenceProperty($property);
            $entityReferenceProperty->setMapping($mapping);
            $targetEntityClass = $mapping['targetEntity'] !== $entityClass ? $mapping['targetEntity'] : $mapping['sourceEntity'];
            $associationAction = $entityReferenceProperty->getActionBasedOnMapping($action);
            $targetEntityReferenceMap = $this->getEntityClassReferenceMeta($targetEntityClass, $associationAction);
            $entityReferenceProperty->setTargetEntityReferenceMap($targetEntityReferenceMap);
            $entityReferenceMap->addPropertyReferences($property, $entityReferenceProperty);
        }
        if (EntityReferenceProperty::actionRequiresCheck($action)) {
            $this->addAdditionalEntityReferenceReferences($entityReferenceMap, $action);
        }
        return $this->entityReferenceMeta[$entityClass];
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param string $entityClass
     * @param string $action
     * @return EntityReferenceMap
     */
    public function getEntityReferenceMetaData(string $entityClass, string $action): EntityReferenceMap
    {
        return $this->getEntityClassReferenceMeta($entityClass, $action);
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param BaseEntityInterface|mixed $object
     * @param EntityReferenceProperty $reference
     * @return mixed|array
     */
    public function getEntityReferenceData($object, EntityReferenceProperty $reference)
    {
        if (!$reference->isMappedOneSided()) {
            return $reference->getObjectIterableValue($object);
        }
        $mapping = $reference->getMapping();
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository($mapping['sourceEntity']);
        $queryBuilder = $repository->createQueryBuilder('lc');
        $queryBuilder->select('lc')
            ->innerJoin('lc.' . $reference->getName(), 'rm')
            ->where('rm.id = :refObject')
            ->setParameter('refObject', $object);
        return $queryBuilder->getQuery()->execute();
    }

}