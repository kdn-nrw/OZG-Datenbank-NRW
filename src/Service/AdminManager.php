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
use App\Model\Annotation\InjectBaseAnnotationReaderTrait;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use App\Util\SnakeCaseConverter;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Psr\Cache\CacheItemPoolInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Cache\ItemInterface;

class AdminManager
{
    use InjectApplicationContextHandlerTrait;
    use InjectCacheTrait;
    use InjectBaseAnnotationReaderTrait;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * MetaItemAdminController constructor.
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * This is public so it can be used in admin instances
     *
     * @return CacheItemPoolInterface
     */
    public function getCache(): CacheItemPoolInterface
    {
        return $this->cache;
    }

    /**
     * Returns the registered admins for the given entity class
     *
     * @param string $entityClass
     * @return array<string>|null
     */
    public function getEntityAdminClasses(string $entityClass): ?array
    {
        $realClass = ClassUtils::getRealClass($entityClass);
        $adminClasses = $this->pool->getAdminClasses();
        return $adminClasses[ltrim($realClass, '\\')] ?? null;
    }

    /**
     * Returns the admin instance with the given id
     *
     * @param string $adminId
     * @return AdminInterface
     */
    public function getAdminInstance(string $adminId): AdminInterface
    {
        return $this->pool->getInstance($adminId);
    }

    /**
     * Returns the entity classes which have an admin service assigned to
     *
     * @return array<string>|string[]
     */
    public function getManagedEntityClasses(): array
    {
        return array_keys($this->pool->getAdminClasses());
    }

    /**
     * Returns the property configuration for the given entity property;
     * The configuration currently contains the admin class and the target entity class of the given property
     * @param BaseEntityInterface|string $entityOrClass
     * @param string $property The name of the entity property (may contain . to get recursive configuration)
     * @return array<string, string> The property configuration
     */
    public function getConfigurationForEntityProperty($entityOrClass, string $property): array
    {
        $tmpClass = is_object($entityOrClass) ? get_class($entityOrClass) : $entityOrClass;
        $entityClass = ClassUtils::getRealClass($tmpClass);
        $key = SnakeCaseConverter::classNameToSnakeCase($entityClass);
        $context = $this->applicationContextHandler->getApplicationContext();
        $cacheKey = $context . '-' . $key . '-' . $property;
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($entityOrClass, $entityClass, $property) {
            $item->expiresAfter(604800);
            $propertyParts = explode('.', $property);
            $isPath = count($propertyParts) > 1;
            $entityProperty = $propertyParts[0];
            $propertyMeta = $this->annotationReader->getEntityPropertyMeta($entityClass, $entityProperty);
            $targetEntityClass = $propertyMeta->getTargetEntity();
            // Fallback functions for finding target class
            if (null === $targetEntityClass && !$isPath) {
                $targetEntityClass = $this->getPropertyTargetClassFromGetterDocComment($entityClass, $entityProperty);
                if (null === $targetEntityClass && is_object($entityOrClass)) {
                    $propertyAccessor = PropertyAccess::createPropertyAccessor();
                    if ($propertyAccessor->isReadable($entityOrClass, $entityProperty)) {
                        $checkValue = $propertyAccessor->getValue($entityOrClass, $entityProperty);
                        if ($checkValue instanceof Collection) {
                            $checkValue = $checkValue->first();
                        }
                        if ($checkValue instanceof BaseEntityInterface) {
                            $targetEntityClass = ClassUtils::getRealClass(get_class($checkValue));
                        }
                    }
                }
            }
            if ($isPath && $targetEntityClass) {
                unset($propertyParts[0]);
                $deepPath = implode('.', $propertyParts);
                $propertyMeta = $this->getConfigurationForEntityProperty($targetEntityClass, $deepPath);
                return $propertyMeta;
            }
            if (null !== $targetEntityClass) {
                $targetAdminClass = $this->getAdminClassForEntityClass($targetEntityClass);
                $defaultLabel = PrefixedUnderscoreLabelTranslatorStrategy::getClassPropertyLabel($entityClass, $entityProperty);
            } else {
                $targetAdminClass = null;
                $defaultLabel = null;
            }
            return [
                'data_type' => $propertyMeta->getTargetDataType(),
                'entity_class' => $targetEntityClass,
                'admin_class' => $targetAdminClass,
                'default_label' => $defaultLabel,
            ];
        });
    }

    /**
     * @param string $entityClass
     * @param string $entityProperty
     * @return string|null
     * @throws \ReflectionException
     */
    private function getPropertyTargetClassFromGetterDocComment(string $entityClass, string $entityProperty): ?string
    {
        $targetEntityClass = null;
        $realClass = ClassUtils::getRealClass($entityClass);
        $entityReflectionClass = new \ReflectionClass($realClass);
        $getter = 'get' . ucfirst($entityProperty);
        if ($entityReflectionClass->hasMethod($getter)) {
            $refMethod = $entityReflectionClass->getMethod($getter);
            if ((false !== $docComment = $refMethod->getDocComment())
                && preg_match('/@return\s+([^\s]+)/', $docComment, $matches)) {
                $returnTypes = array_filter(explode('|', $matches[1]), static function ($var) {
                    return !in_array($var, ['ArrayCollection', 'Collection', 'null', 'array']);
                });
                if (!empty($returnTypes)) {
                    $namespace = $entityReflectionClass->getNamespaceName();
                    $returnType = $namespace . '\\' . str_replace(['[', ']'], '', $returnTypes[0]);
                    if (class_exists($returnType)) {
                        $targetEntityClass = $returnType;
                    }
                }
            }
        }
        return $targetEntityClass;
    }

    /**
     * Returns the admin for the given entity class depending the current application context
     * @param string $entityClass
     * @return AdminInterface|null
     */
    public function getAdminByEntityClass(string $entityClass): ?AdminInterface
    {
        $adminClass = $this->getAdminClassForEntityClass($entityClass);
        if ($adminClass) {
            return $this->getAdminInstance($adminClass);
        }
        return null;
    }

    /**
     * Returns the admin instance for the given entity class.
     * If multiple admins are defined for the class, the admin for the current applicant context is returned
     * If no admins are registered for this class, null is returned
     *
     * @param string $entityClass The fully qualified name of the entity class
     * @return string|null The admin class or null
     */
    public function getAdminClassForEntityClass(string $entityClass): ?string
    {
        $objectAdminClasses = $this->getEntityAdminClasses($entityClass);
        $adminClass = null;
        if (!empty($objectAdminClasses)) {
            if (count($objectAdminClasses) > 1) {
                $isBackendMode = $this->applicationContextHandler->isBackend();
                $keyword = '\\Frontend\\';
                foreach ($objectAdminClasses as $contextAdminClass) {
                    if ($isBackendMode && strpos($contextAdminClass, $keyword) === false) {
                        $adminClass = $contextAdminClass;
                        break;
                    }
                    if (!$isBackendMode && strpos($contextAdminClass, $keyword) !== false) {
                        $adminClass = $contextAdminClass;
                        break;
                    }
                }
            } else {
                $adminClass = $objectAdminClasses[0];
            }
        }
        return $adminClass;
    }
}