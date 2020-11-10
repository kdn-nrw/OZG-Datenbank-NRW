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

namespace App\Import\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use ReflectionClass;

/**
 * Class AnnotationReader
 */
class AnnotationReader
{
    /**
     * Cache for model annotations
     *
     * @var array
     */
    private $annotationCache = [];

    /**
     * Returns the api form model annotations
     *
     * @param string $modelClass The model class
     * @return ImportModelAnnotation[]|Annotation[]|array
     * @throws \ReflectionException
     */
    public function getModelPropertyConfiguration(string $modelClass): array
    {
        if (!array_key_exists($modelClass, $this->annotationCache)) {
            $annotations = [];
            $annotationReader = new CachedReader(new \Doctrine\Common\Annotations\AnnotationReader(), new ArrayCache());
            $reflectionClass = new ReflectionClass($modelClass);
            $classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
            $defaultTargetEntity = null;
            foreach ($classAnnotations as $classAnnotation) {
                if ($classAnnotation instanceof ImportModelAnnotation) {
                    $defaultTargetEntity = $classAnnotation->getTargetEntity();
                }
            }
            $properties = $reflectionClass->getProperties();
            foreach ($properties as $property) {
                $propertyAnnotations = $annotationReader->getPropertyAnnotations($property);
                foreach ($propertyAnnotations as $propertyAnnotation) {
                    if ($propertyAnnotation instanceof ImportModelAnnotation) {
                        $annotations[$property->getName()] = $propertyAnnotation;
                        continue;
                    }
                }
            }
            if ($defaultTargetEntity) {
                $this->extendAnnotations($defaultTargetEntity, $annotations, $annotationReader);
            }
            $this->annotationCache[$modelClass] = $annotations;
        }
        return $this->annotationCache[$modelClass];
    }

    /**
     * @param string $targetEntity
     * @param ImportModelAnnotation[]|array $annotations
     * @param CachedReader $annotationReader
     * @throws \ReflectionException
     */
    private function extendAnnotations(
        string $targetEntity,
        array $annotations,
        CachedReader $annotationReader)
    {
        $entityReflectionClass = new ReflectionClass($targetEntity);
        foreach ($annotations as $propertyName => $propertyAnnotation) {
            if (!$propertyAnnotation->getTargetEntity()) {
                $propertyAnnotation->setTargetEntity($targetEntity);
            }
            if ($entityReflectionClass->hasProperty($propertyName)) {
                $entityProperty = $entityReflectionClass->getProperty($propertyName);
                $entityPropertyAnnotations = $annotationReader->getPropertyAnnotations($entityProperty);
                foreach ($entityPropertyAnnotations as $entityPropertyAnnotation) {
                    if ($entityPropertyAnnotation instanceof Column) {
                        $propertyAnnotation->setTargetDataType($entityPropertyAnnotation->type);
                    } elseif ($entityPropertyAnnotation instanceof ManyToOne) {
                        $propertyAnnotation->setTargetEntity($entityPropertyAnnotation->targetEntity);
                    }
                }
            }
        }
    }
}