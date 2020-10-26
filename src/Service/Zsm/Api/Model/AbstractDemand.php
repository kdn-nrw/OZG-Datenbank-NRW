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

namespace App\Service\Zsm\Api\Model;

abstract class AbstractDemand
{
    protected const REQUEST_METHOD_GET = 'GET';

    /**
     *
     * @var DemandParameter[]|array
     */
    protected $parameterRegistry = [];

    /**
     * The demand search values
     *
     * @var array
     */
    protected $values = [];

    /**
     * AbstractDemand constructor.
     */
    public function __construct()
    {
        $this->initializeParameterRegistry();
    }

    /**
     * Initialize the parameter registry (list of allowed parameters)
     */
    abstract protected function initializeParameterRegistry(): void;

    /**
     * Adds the given parameter to the registry
     *
     * @param DemandParameter $parameter
     */
    public function registerParameter(DemandParameter $parameter): void
    {
        $this->parameterRegistry[$parameter->getName()] = $parameter;
    }

    /**
     * @return DemandParameter[]|array
     */
    public function getParameterRegistry(): array
    {
        return $this->parameterRegistry;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function getRequestMethod(): string
    {
        return self::REQUEST_METHOD_GET;
    }
}