<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Statistics;

/**
 * Interface for statistics providers
 * Concrete sub-classes must either implement ExportStatisticsProviderInterface or ChartStatisticsProviderInterface
 */
abstract class AbstractStatisticsProvider
{
    /**
     * Unique provider key
     *
     * @var string
     */
    private $key;

    /**
     * Provider type (excel|csv|chart)
     * @var string
     */
    protected $type;

    /**
     * Automatically determined translation prefix for this provider
     *
     * @var string
     */
    protected $translationPrefix;

    /**
     * AbstractStatisticsProvider constructor.
     */
    public function __construct()
    {
        $class = str_replace('Bundle', '', get_class($this));
        $string = preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $class);
        $lccClass = strtolower($string);
        $this->translationPrefix = str_replace('\\', '.', strtolower($lccClass));
        $tmpKey = str_replace('\\', '-', strtolower($lccClass));
        $this->key = str_replace(['_', '-provider'], ['-', ''], $tmpKey);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return AbstractStatisticsProvider
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->translationPrefix . '.label';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->translationPrefix . '.description';
    }

    /**
     * Load statistical data
     *
     * @return mixed
     */
    abstract protected function loadData();

    /**
     * Override this function to allow setting custom options for the statistics provider
     * @param array $options
     */
    public function setOptions(array $options)
    {

    }

}