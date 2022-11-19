<?php
declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping;
use ReflectionClass;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Class BaseAnnotationReader
 */
class BaseAnnotationReader
{
    /**
     * @var Reader
     */
    private $annotationReader;

    protected function getReader(): Reader
    {
        if (null === $this->annotationReader) {
            $reader = new AnnotationReader();
            $this->annotationReader = new PsrCachedReader($reader, new ArrayAdapter());
        }
        return $this->annotationReader;
    }

    /**
     * Returns basic meta information about the given property belonging to the given class
     * The metadata contains the data type and if set, the target entity for Doctrine ORM references
     *
     * @param string $entityClass
     * @param string $propertyName
     * @return BaseModelAnnotation
     * @throws \ReflectionException
     */
    public function getEntityPropertyMeta(string $entityClass, string $propertyName): BaseModelAnnotation
    {
        $entityReflectionClass = new ReflectionClass($entityClass);
        $propertyAnnotation = new BaseModelAnnotation(['mapToProperty' => $propertyName]);
        $this->extendPropertyAnnotation(
            $entityClass,
            $propertyName,
            $propertyAnnotation,
            $entityReflectionClass,
            false
        );
        return $propertyAnnotation;
    }

    /**
     * Create api annotation model from default property definitions for additional properties that
     * don't have a ApiProviderModelAnnotation defined
     * @param string $entityClass
     * @param string $propertyName
     * @param BaseModelAnnotation $propertyAnnotation
     * @param ReflectionClass|null $entityReflectionClass
     * @param bool $setDefaultTargetEntity
     * @throws \ReflectionException
     */
    protected function extendPropertyAnnotation(
        string $entityClass,
        string $propertyName,
        BaseModelAnnotation $propertyAnnotation,
        ?ReflectionClass $entityReflectionClass = null,
        bool $setDefaultTargetEntity = true
    ): void
    {
        if (null === $entityReflectionClass) {
            $entityReflectionClass = new ReflectionClass($entityClass);
        }
        $annotationReader = $this->getReader();
        if ($setDefaultTargetEntity && !$propertyAnnotation->getTargetEntity()) {
            $propertyAnnotation->setTargetEntity($entityClass);
        }
        if ($entityReflectionClass->hasProperty($propertyName)) {
            $entityProperty = $entityReflectionClass->getProperty($propertyName);
            $entityPropertyAnnotations = $annotationReader->getPropertyAnnotations($entityProperty);
            foreach ($entityPropertyAnnotations as $entityPropertyAnnotation) {
                if ($entityPropertyAnnotation instanceof Mapping\Column) {
                    if (!$propertyAnnotation->getDataType()) {
                        $propertyAnnotation->setDataType($entityPropertyAnnotation->type);
                    }
                    $propertyAnnotation->setTargetDataType($entityPropertyAnnotation->type);
                    if (property_exists($entityPropertyAnnotation, 'nullable')) {
                        $propertyAnnotation->setRequired(!$entityPropertyAnnotation->nullable);
                    }
                } elseif ($entityPropertyAnnotation instanceof Mapping\ManyToOne
                    || $entityPropertyAnnotation instanceof Mapping\OneToOne) {
                    $propertyAnnotation->setTargetEntity($entityPropertyAnnotation->targetEntity);
                    if (property_exists($entityPropertyAnnotation, 'nullable')) {
                        $propertyAnnotation->setRequired(!$entityPropertyAnnotation->nullable);
                    }
                    $propertyAnnotation->setTargetDataType(BaseModelAnnotation::DATA_TYPE_MODEL);
                } elseif ($entityPropertyAnnotation instanceof Mapping\ManyToMany) {
                    $propertyAnnotation->setTargetEntity($entityPropertyAnnotation->targetEntity);
                    $propertyAnnotation->setTargetDataType(BaseModelAnnotation::DATA_TYPE_COLLECTION);
                } elseif ($entityPropertyAnnotation instanceof Mapping\OneToMany) {
                    $propertyAnnotation->setTargetEntity($entityPropertyAnnotation->targetEntity);
                    $propertyAnnotation->setTargetDataType(BaseModelAnnotation::DATA_TYPE_COLLECTION);
                }
            }
        }
        // Fix target entity definition without namespace
        if (($targetEntityClass = $propertyAnnotation->getTargetEntity()) && strpos($targetEntityClass, '\\') === false) {
            $ecParts = explode('\\', $entityClass);
            array_pop($ecParts);
            $ecParts[] = $targetEntityClass;
            $propertyAnnotation->setTargetEntity(implode('\\', $ecParts));
        }
    }
}