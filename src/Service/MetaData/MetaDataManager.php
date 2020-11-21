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

namespace App\Service\MetaData;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\MetaData\AbstractMetaItem;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\MetaData\MetaItem;
use App\Entity\MetaData\MetaItemProperty;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use App\Translator\TranslatorAwareTrait;
use App\Util\SnakeCaseConverter;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Contracts\Translation\TranslatorInterface;

class MetaDataManager
{
    use InjectManagerRegistryTrait;
    use TranslatorAwareTrait;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * MetaItemAdminController constructor.
     * @param TranslatorInterface $translator
     * @param Pool $pool
     */
    public function __construct(
        TranslatorInterface $translator,
        Pool $pool
    )
    {
        $this->setTranslator($translator);
        $this->pool = $pool;
    }

    public function createMetaItems(): void
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository(MetaItem::class);
        $metaItems = $repository->findAll();
        $groupedItems = [
            AbstractMetaItem::META_TYPE_ENTITY => [],
        ];
        /** @var MetaItem $metaItem */
        foreach ($metaItems as $metaItem) {
            $groupedItems[$metaItem->getMetaType()][$metaItem->getMetaKey()] = $metaItem;
        }
        $metaType = AbstractMetaItem::META_TYPE_ENTITY;
        // TODO 1: persist registered entities
        $allMetaData = $em->getMetadataFactory()->getAllMetadata();
        foreach ($allMetaData as $metaData) {
            $entityClass = $metaData->getName();
            if (is_a($entityClass, HasMetaDateEntityInterface::class, true)) {
                $metaKey = SnakeCaseConverter::classNameToSnakeCase($entityClass);
                if ((empty($groupedItems[$metaType]) || !array_key_exists($metaKey, $groupedItems[$metaType]))) {
                    $metaItem = new MetaItem();
                    $metaItem->setMetaType($metaType);
                    $metaItem->setMetaKey($metaKey);
                    $metaItem->setInternalLabel($metaKey);
                    $em->persist($metaItem);
                    $groupedItems[$metaType][$metaKey] = $metaItem;
                } else {
                    $metaItem = $groupedItems[$metaType][$metaKey];
                    /** @var MetaItem $metaItem */
                }
                $label = PrefixedUnderscoreLabelTranslatorStrategy::getClassPropertyLabel($entityClass);
                if ($label !== $metaItem->getInternalLabel()) {
                    $metaItem->setInternalLabel($label);
                }
                $this->addMetaProperties($metaItem, $entityClass);
            }
        }
        // TODO 2: get properties for these entities
        // TODO 3: get properties for list view fields
        // TODO 4: get properties for show view fields
        // TODO 5: predefine all properties
        $em->flush();

    }

    /**
     * Adds the meta properties for the given meta item: adds list and show fields of all entity admins for this class
     *
     * @param MetaItem $metaItem
     * @param string $entityClass
     * @throws \Doctrine\ORM\ORMException
     * @throws \ReflectionException
     */
    private function addMetaProperties(MetaItem $metaItem, string $entityClass): void
    {
        $adminClasses = $this->pool->getAdminClasses();
        $reflectionClass = new \ReflectionClass($entityClass);
        $properties = $reflectionClass->getProperties();
        $entityPropertyNames = [];
        foreach ($properties as $property) {
            $entityPropertyNames[] = $property->getName();
        }
        $objectAdminClasses = $adminClasses[ltrim($entityClass, '\\')] ?? null;
        if (!empty($objectAdminClasses)) {
            foreach ($objectAdminClasses as $adminClass) {
                $admin = $this->pool->getInstance($adminClass);
                /** @var AbstractAdmin $admin */
                $this->addFieldDescriptions($metaItem, $admin->getList(), $entityPropertyNames);
                $this->addFieldDescriptions($metaItem, $admin->getShow(), $entityPropertyNames);
            }
        }
    }

    /**
     * Add field description meta properties
     *
     * @param MetaItem $metaItem
     * @param FieldDescriptionCollection|null $fieldDescriptions
     * @param array $entityPropertyNames
     * @throws \Doctrine\ORM\ORMException
     */
    private function addFieldDescriptions(
        MetaItem $metaItem,
        ?FieldDescriptionCollection $fieldDescriptions,
        array $entityPropertyNames): void
    {
        $em = $this->getEntityManager();
        if (null !== $fieldDescriptions) {
            foreach ($fieldDescriptions->getElements() as $fieldDescription) {
                /** @var FieldDescriptionInterface $fieldDescription */
                $name = $fieldDescription->getName();
                if ($name === '_action') {
                    continue;
                }
                $label = $fieldDescription->getOption('label');
                $property = $metaItem->getMetaItemProperty($name);
                if (null === $property) {
                    $property = new MetaItemProperty();
                    $metaKey = SnakeCaseConverter::camelCaseToSnakeCase($name);
                    $property->setMetaKey($metaKey);
                    $metaType = in_array($name, $entityPropertyNames, false)
                        ? AbstractMetaItem::META_TYPE_FIELD
                        : AbstractMetaItem::META_TYPE_ADMIN_FIELD;
                    $property->setMetaType($metaType);
                    $em->persist($property);
                    $metaItem->addMetaItemProperty($property);
                }
                if ($label !== $property->getInternalLabel()) {
                    $property->setInternalLabel($label);
                }
            }
        }
    }
}