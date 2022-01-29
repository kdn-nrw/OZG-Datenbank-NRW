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

use App\Admin\Onboarding\AbstractOnboardingAdmin;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\MetaData\AbstractMetaItem;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\MetaData\MetaItem;
use App\Entity\MetaData\MetaItemProperty;
use App\Service\InjectAdminManagerTrait;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use App\Translator\TranslatorAwareTrait;
use App\Util\SnakeCaseConverter;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Contracts\Translation\TranslatorInterface;

class MetaDataManager
{
    use InjectAdminManagerTrait;
    use InjectManagerRegistryTrait;
    use TranslatorAwareTrait;

    /**
     * @var array|MetaItem[]
     */
    protected $metaCache = [];

    /**
     * MetaItemAdminController constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    /**
     * Create meta data for all entities that implement HasMetaDateEntityInterface; add all properties defined in the
     * admin (if an admin class exists)
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
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
        $em->flush();

    }

    /**
     * Returns the meta data for entity classes that are referenced in another entity
     *
     * @param string|BaseEntityInterface $objectOrClass
     * @param string $property
     * @return MetaItem|null
     */
    public function getObjectPropertyReferenceClassMetaData($objectOrClass, string $property): ?MetaItem
    {
        $propertyConfiguration = $this->adminManager->getConfigurationForEntityProperty($objectOrClass, $property);
        if (array_key_exists('entity_class', $propertyConfiguration) && !empty($propertyConfiguration['entity_class'])) {
            return $this->getObjectClassMetaData($propertyConfiguration['entity_class']);
        }
        return null;
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
        $reflectionClass = new \ReflectionClass($entityClass);
        $properties = $reflectionClass->getProperties();
        $entityPropertyNames = [];
        foreach ($properties as $property) {
            $entityPropertyNames[] = $property->getName();
        }
        $objectAdminClasses = $this->adminManager->getEntityAdminClasses($entityClass);
        if (!empty($objectAdminClasses)) {
            foreach ($objectAdminClasses as $adminClass) {
                $admin = $this->adminManager->getAdminInstance($adminClass);
                /** @var AbstractAdmin $admin */
                $this->addFieldDescriptions($metaItem, $admin->getList(), $entityPropertyNames);
                $this->addFieldDescriptions($metaItem, $admin->getShow(), $entityPropertyNames);
                if ($admin instanceof AbstractOnboardingAdmin) {
                    $this->addFormGroupsAndTabs($metaItem, $admin);
                }
            }
        }
    }

    /**
     * Add meta properties for form groups and tabs
     *
     * @param MetaItem $metaItem
     * @param AbstractAdmin $admin
     * @throws \Doctrine\ORM\ORMException
     */
    protected function addFormGroupsAndTabs(MetaItem $metaItem, AbstractAdmin $admin): void
    {
        $em = $this->getEntityManager();
        $admin->getFormBuilder();
        $data = [
            AbstractMetaItem::META_TYPE_GROUP => $admin->getFormGroups(),
            AbstractMetaItem::META_TYPE_TAB => $admin->getFormTabs()
        ];
        foreach ($data as $metaType => $metaTypeData) {
            if (empty($metaTypeData)) {
                continue;
            }
            foreach ($metaTypeData as $name => $options) {
                $groupKey = SnakeCaseConverter::camelCaseToSnakeCase(str_replace('.', '_', $name));
                $metaKey = $metaType . '_' . $groupKey;
                $property = $metaItem->getMetaItemProperty($metaKey);
                $label = $options['label'];
                if (!$label && $itemLabel = $metaItem->getInternalLabel()) {
                    $label = str_replace('.list', '', $itemLabel)
                        . '.' . $metaType . 's.' . str_replace('.', '_', $groupKey);
                }
                if (null === $property) {
                    $property = new MetaItemProperty();
                    $property->setMetaType($metaType);
                    $property->setMetaKey($metaKey);
                    $em->persist($property);
                    $metaItem->addMetaItemProperty($property);
                }
                if (!$property->getDescription() && $options['description']) {
                    $property->setDescription($admin->trans($options['description']));
                }
                if ($label && $label !== $property->getInternalLabel()) {
                    $property->setInternalLabel($label);
                }
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

    /**
     * Returns the meta data for the entity class managed by this admin; returns null if entity has no meta data
     * @param object|string $objectOrClass
     * @return MetaItem|null
     */
    public function getObjectClassMetaData($objectOrClass): ?MetaItem
    {
        $objectClassName = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;
        $metaKey = SnakeCaseConverter::classNameToSnakeCase($objectClassName);
        if (!array_key_exists($metaKey, $this->metaCache)) {
            $metaItem = null;
            if (is_a($objectClassName, HasMetaDateEntityInterface::class, true)) {
                $em = $this->getEntityManager();
                /** @var ModelManager $modelManager */
                $repository = $em->getRepository(MetaItem::class);
                $metaItem = $repository->findOneBy(['metaKey' => $metaKey]);
                /** @var MetaItem|null $metaItem */
            }
            $this->metaCache[$metaKey] = $metaItem;
        }
        return $this->metaCache[$metaKey];
    }
}