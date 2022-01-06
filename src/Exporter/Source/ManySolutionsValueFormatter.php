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

use App\Entity\HasSolutionsEntityInterface;
use App\Entity\Service;
use App\Entity\Solution;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Custom formatter for entities with many solutions
 *
 * @package App\Exporter\Source
 */
class ManySolutionsValueFormatter extends ServiceSolutionValueFormatter
{
    /**
     * @var bool
     */
    public $showServiceKeys = false;

    /**
     * @param bool $showServiceKeys
     */
    public function setShowServiceKeys(bool $showServiceKeys): void
    {
        $this->showServiceKeys = $showServiceKeys;
    }

    /**
     * Returns the property value for the given object or array
     *
     * @param string $propertyPath The name of the property
     * @param Service|Solution|object|array $objectOrArray
     * @return string|null
     */
    public function getPropertyValue(string $propertyPath, $objectOrArray): ?string
    {
        if ($objectOrArray instanceof HasSolutionsEntityInterface) {
            $collection = $objectOrArray->getSolutions();
            switch ($this->displayType) {
                case self::DISPLAY_SERVICE_KEY:
                    $serviceSolutions = $this->getUniqueServiceSolutionCollection($collection);
                    $value = $this->createServiceKeyValue($serviceSolutions);
                    break;
                case self::DISPLAY_SERVICE_NAME:
                    $serviceSolutions = $this->getUniqueServiceSolutionCollection($collection);
                    $value = $this->createServiceNameValue($serviceSolutions);
                    break;
                default:
                    $value = $this->getSolutionListValue($collection);
                    break;
            }
            return $value;
        }
        return null;
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    protected function getUniqueServiceSolutionCollection($collection): Collection
    {
        $serviceSolutions = new ArrayCollection();
        foreach ($collection as $entity) {
            /** @var Solution $entity */
            $entityServiceSolutions = $entity->getServiceSolutions();
            foreach ($entityServiceSolutions as $serviceSolution) {
                if (!$serviceSolutions->contains($serviceSolution)) {
                    $serviceSolutions->add($serviceSolution);
                }
            }
        }
        return $serviceSolutions;
    }

    /**
     * @param Collection $collection
     * @return string|null
     */
    protected function getSolutionListValue($collection): ?string
    {
        $valueList = [];
        foreach ($collection as $entity) {
            /** @var Solution $entity */
            $entryValue = $entity . '';
            if ($this->showServiceKeys) {
                $serviceKeys = $entity->getServiceSolutions();
                $serviceKeyValue = $this->createServiceKeyValue($serviceKeys);
                if ($serviceKeyValue) {
                    $entryValue .= ' (' . $serviceKeyValue . ')';
                }
            }
            $valueList[] = $entryValue;
        }
        return implode(',', $valueList);
    }
}