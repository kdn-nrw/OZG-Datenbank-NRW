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

namespace App\Exporter\Source;

use App\Entity\AbstractService;
use App\Entity\ImplementationProject;
use App\Entity\ImplementationProjectService;
use App\Entity\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ServiceListValueFormatter implements CustomPropertyValueFormatterInterface
{
    public const DISPLAY_TYPE_DEFAULT = 0;
    public const DISPLAY_SERVICE_KEY = 1;
    public const DISPLAY_SERVICE_NAME = 2;

    /**
     * Toggle between display of services and solutions
     *
     * @var int
     */
    protected $displayType = self::DISPLAY_TYPE_DEFAULT;

    /**
     * @param int $displayType
     */
    public function setDisplayType(int $displayType): void
    {
        $this->displayType = $displayType;
    }

    /**
     * Returns the property value for the given object or array
     *
     * @param string $propertyPath
     * @param ImplementationProject|object|array $objectOrArray
     * @return string|null
     */
    public function getPropertyValue(string $propertyPath, $objectOrArray): ?string
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        switch ($propertyPath) {
            case 'serviceSystemKeys':
                $realProperty = 'serviceSystems';
                break;
            case 'serviceKeys':
                $realProperty = 'services';
                break;
            default:
                $realProperty = $propertyPath;
        }
        $collection = $propertyAccessor->getValue($objectOrArray, $realProperty);
        if (!is_iterable($collection)) {
            throw new \InvalidArgumentException('The property %s must be iterable', $realProperty);
        }
        $firstItem = null;
        if ($collection instanceof Collection) {
            $firstItem = $collection->first();
        } elseif (!empty($collection)) {
            $firstItem = $collection[0];
        }
        if ($firstItem instanceof ImplementationProjectService) {
            $collection = $this->getServiceCollection($collection);
        }
        switch ($this->displayType) {
            case self::DISPLAY_SERVICE_KEY:
                $value = $this->createServiceKeyValue($collection);
                break;
            case self::DISPLAY_SERVICE_NAME:
                $value = $this->createServiceNameValue($collection);
                break;
            default:
                $valueList = [];
                foreach ($collection as $entity) {
                    $valueList[] = $entity . '';
                }
                $value = implode(',', $valueList);
                break;
        }
        return $value;
    }

    /**
     * Transform project service to service collection
     * @param ImplementationProjectService[]|Collection $collection
     * @return Service[]|Collection
     */
    private function getServiceCollection(Collection $collection): Collection
    {
        $serviceCollection = new ArrayCollection();
        foreach ($collection as $entity) {
            /** @var ImplementationProjectService $entity */
            if ((null !== $service = $entity->getService()) && !$serviceCollection->contains($service)) {
                $serviceCollection->add($service);
            }
        }
        return $serviceCollection;

    }

    /**
     * Returns a comma-separated list of service keys
     *
     * @param Collection $collection
     * @return string
     */
    protected function createServiceKeyValue(Collection $collection): string
    {
        $valueList = [];
        foreach ($collection as $entity) {
            /** @var AbstractService $entity */
            $serviceKey = $entity->getServiceKey();
            $valueList[$serviceKey] = $serviceKey;
        }
        ksort($valueList);
        return implode(', ', $valueList);
    }

    /**
     * Returns a comma-separated list of service names
     *
     * @param Collection $collection
     * @return string
     */
    protected function createServiceNameValue(Collection $collection): string
    {
        $valueList = [];
        foreach ($collection as $entity) {
            /** @var AbstractService $entity */
            $serviceKey = $entity->getServiceKey();
            $valueList[$serviceKey] = sprintf("%s (%s)", $entity, $serviceKey);
        }
        ksort($valueList);
        return implode(', ', $valueList);
    }
}