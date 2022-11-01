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

use App\Model\Annotation\BaseAnnotationReader;
use App\Model\Annotation\BaseModelAnnotation;
use Doctrine\Common\Annotations\Annotation;
use ReflectionClass;

/**
 * Class AnnotationReader
 */
class AnnotationReader extends BaseAnnotationReader
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
            $annotationReader = $this->getReader();
            $reflectionClass = new ReflectionClass($modelClass);
            $classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
            $defaultTargetEntity = null;
            foreach ($classAnnotations as $classAnnotation) {
                if ($classAnnotation instanceof BaseModelAnnotation) {
                    $defaultTargetEntity = $classAnnotation->getTargetEntity();
                }
            }
            $properties = $reflectionClass->getProperties();
            foreach ($properties as $property) {
                $propertyAnnotations = $annotationReader->getPropertyAnnotations($property);
                foreach ($propertyAnnotations as $propertyAnnotation) {
                    if ($propertyAnnotation instanceof BaseModelAnnotation) {
                        $annotations[$property->getName()] = $propertyAnnotation;
                    }
                }
            }
            if ($defaultTargetEntity) {
                $this->extendAnnotations($defaultTargetEntity, $annotations);
            }
            $this->annotationCache[$modelClass] = $annotations;
        }
        return $this->annotationCache[$modelClass];
    }

    /**
     * @param string $targetEntity
     * @param BaseModelAnnotation[]|array $annotations
     *
     * @throws \ReflectionException
     */
    private function extendAnnotations(string $targetEntity, array $annotations): void
    {
        $entityReflectionClass = new ReflectionClass($targetEntity);
        foreach ($annotations as $propertyName => $propertyAnnotation) {
            $this->extendPropertyAnnotation(
                $targetEntity,
                $propertyName,
                $propertyAnnotation,
                $entityReflectionClass
            );
        }
    }
}