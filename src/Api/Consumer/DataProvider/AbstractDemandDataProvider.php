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

namespace App\Api\Consumer\DataProvider;

use App\Api\Annotation\ApiSearchModelAnnotation;
use App\Api\Consumer\Model\AbstractDemand;
use App\Import\Annotation\InjectAnnotationReaderTrait;
use App\Import\DataProvider\DataProviderInterface;

abstract class AbstractDemandDataProvider implements DataProviderInterface, DemandDataProviderInterface
{
    use InjectAnnotationReaderTrait;

    /**
     * @var AbstractDemand
     */
    protected $demand;

    /**
     * @param AbstractDemand $demand
     */
    public function setDemand(AbstractDemand $demand): void
    {
        $this->demand = $demand;
    }

    /**
     * Returns the API demand instance
     *
     * @return AbstractDemand
     */
    public function getDemand(): AbstractDemand
    {
        return $this->demand;
    }

    /**
     * Returns the api demand model configuration
     *
     * @return ApiSearchModelAnnotation[]|array
     */
    public function getDemandPropertyConfiguration(): array
    {
        return $this->annotationReader->getModelPropertyConfiguration(get_class($this->getDemand()));
    }

}
