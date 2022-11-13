<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Exporter\Source;

use App\Entity\Base\BaseEntityInterface;
use App\Entity\Service;
use App\Entity\Solution;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Custom formatter for entities with many sub-entities
 *
 * @package App\Exporter\Source
 */
class ManyEntitiesValueFormatter implements CustomPropertyValueFormatterInterface
{
    /**
     * Returns the property value for the given object or array
     *
     * @param string $propertyPath The name of the property
     * @param Service|Solution|object|array $objectOrArray
     * @return string|null
     */
    public function getPropertyValue(string $propertyPath, $objectOrArray): ?string
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if ($propertyAccessor->isReadable($objectOrArray, $propertyPath)) {
            $collection = $propertyAccessor->getValue($objectOrArray, $propertyPath);
            if ($collection instanceof Collection) {
                return $this->getEntityListValue($collection);
            }
        }
        return null;
    }

    /**
     * @param Collection $collection
     * @return string|null
     */
    protected function getEntityListValue(Collection $collection): ?string
    {
        $valueList = [];
        foreach ($collection as $entity) {
            /** @var BaseEntityInterface $entity */
            $entryValue = $entity . '';
            $valueList[] = $entryValue;
        }
        return implode(',', $valueList);
    }
}