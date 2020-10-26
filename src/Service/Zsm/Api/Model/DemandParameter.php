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

class DemandParameter
{
    public const TYPE_STRING = 'string';

    /**
     * The parameter name
     *
     * @var string
     */
    protected $name;

    /**
     * The parameter label
     *
     * @var string
     */
    protected $label;

    /**
     * Parameter is required
     *
     * @var bool
     */
    protected $required = false;

    /**
     * The parameter data type
     *
     * @var string
     */
    protected $dataType = self::TYPE_STRING;

    public function __construct(string $name, string $label, bool $required = false, string $dataType = self::TYPE_STRING)
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->dataType = $dataType;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

}