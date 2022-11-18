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

namespace App\Exporter\Source;

use App\Entity\Service;
use App\Entity\ServiceSolution;
use App\Entity\Solution;
use Doctrine\Common\Collections\Collection;

class ServiceSolutionValueFormatter implements CustomPropertyValueFormatterInterface
{
    public const DISPLAY_TYPE_DEFAULT = 0;
    public const DISPLAY_SERVICE_KEY = 1;
    public const DISPLAY_SERVICE_NAME = 2;
    public const DISPLAY_SOLUTION_NAME = 10;

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
     * @param Service|Solution|object|array $objectOrArray
     * @return string|null
     */
    public function getPropertyValue(string $propertyPath, $objectOrArray): ?string
    {
        if (!is_object($objectOrArray)) {
            return '';
        }
        /** @var Service|Solution $objectOrArray */
        if ($propertyPath === 'serviceSolutions') {
            $collection = $objectOrArray->getServiceSolutions();
        } else {
            $collection = $objectOrArray->getPublishedServiceSolutions();
        }
        switch ($this->displayType) {
            case self::DISPLAY_SERVICE_KEY:
                $value = $this->createServiceKeyValue($collection);
                break;
            case self::DISPLAY_SERVICE_NAME:
                $value = $this->createServiceNameValue($collection);
                break;
            case self::DISPLAY_SOLUTION_NAME:
                $value = $this->createSolutionNameValue($collection);
                break;
            default:
                $valueList = [];
                foreach ($collection as $entity) {
                    $valueList[] = $entity . '';
                }
                $value = implode(',' , $valueList);
                break;
        }
        return $value;
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
            /** @var ServiceSolution $entity */
            if (null !== $service = $entity->getService()) {
                $valueList[$service->getServiceKey()] = $service->getServiceKey();
            }
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
            /** @var ServiceSolution $entity */
            if (null !== $service = $entity->getService()) {
                $valueList[$service->getServiceKey()] = $service . ' ('.$service->getServiceKey().')';
            }
        }
        ksort($valueList);
        return implode(', ', $valueList);
    }

    /**
     * Returns a comma-separated list of solution names
     *
     * @param Collection $collection
     * @return string
     */
    protected function createSolutionNameValue(Collection $collection): string
    {
        $valueList = [];
        foreach ($collection as $entity) {
            /** @var ServiceSolution $entity */
            if (null !== $solution = $entity->getSolution()) {
                $valueList[] = $solution . '';
            }
        }
        return implode(', ', $valueList);
    }
}