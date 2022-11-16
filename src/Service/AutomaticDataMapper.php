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

namespace App\Service;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Application\ApplicationServiceAutoMapping;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;

/**
 * Used for automatic mapping of entity relations
 */
final class AutomaticDataMapper
{
    private $internalMappingCache = [];

    use InjectManagerRegistryTrait;

    /**
     * Save auto-assigned relation of application services
     * @param array<string, array> $dataMap
     * @return void
     */
    public function addAutoAssignedApplicationServices(array $dataMap)
    {
        if (!empty($dataMap)) {
            $cachePrefix = 'sp';
            $asAutoEm = $this->getEntityManager();
            foreach ($dataMap as $dataRow) {
                /** @var Solution $solution */
                $solution = $dataRow['solution'];
                /** @var SpecializedProcedure $specializedProcedure */
                $specializedProcedure = $dataRow['specializedProcedure'];
                $cacheKey = $cachePrefix . $specializedProcedure->getId();
                if (!isset($this->internalMappingCache[$cacheKey])) {
                    // Use normal query for performance
                    $query = 'SELECT service_id FROM ozg_meta_service_specialized_procedure WHERE specialized_procedure_id = ?';
                    $mappedServiceIds = $asAutoEm->getConnection()->fetchFirstColumn($query, [$specializedProcedure->getId()]);
                    if (empty($mappedServiceIds)) {
                        $mappedServiceIds = [];
                    }
                    $this->internalMappingCache[$cacheKey] = $mappedServiceIds;
                } else  {
                    $mappedServiceIds = $this->internalMappingCache[$cacheKey];
                }
                foreach ($solution->getServiceSolutions() as $serviceSolution) {
                    if ((null !== $service = $serviceSolution->getService())
                        && !in_array($service->getId(), $mappedServiceIds, false)) {
                        // Save "new" services in extra table, so we can track, which service has been added before
                        // Otherwise we can't determine, if a service has been removed manually from an application and
                        // would then be added again here
                        $mapEntity = new ApplicationServiceAutoMapping();
                        $mapEntity->setSpecializedProcedure($specializedProcedure);
                        $mapEntity->setService($service);
                        $mapEntity->setSolution($solution);
                        if ($specializedProcedure->getServices()->contains($service)) {
                            $mapEntity->setDisabled(true);
                        }
                        // TODO: first make sure, that this is actually really wanted by the customer!
                        //$specializedProcedure->addService($service);
                        $asAutoEm->persist($mapEntity);
                    }
                }
            }
        }
    }

}