<?php
declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class ApiSearchModelAnnotation
 *
 * @Annotation
 * @Target({"PROPERTY"})
 * @Required
 */
class ApiSearchModelAnnotation extends Annotation
{
    public const DATA_TYPE_STRING = 'string';
    public const DATA_TYPE_INT = 'int';
    public const DATA_TYPE_FLOAT = 'float';
    public const DATA_TYPE_MODEL = 'model';
    public const DATA_TYPE_MODEL_COLLECTION = 'modelCollection';

    /**
     * @var string
     */
    public $parameter;

    /**
     * @var string
     */
    public $dataType;

    /**
     * @var bool
     */
    public $required;

    /**
     * Toggle custom status for property; Determines if property is added to the default form fields
     * @var bool
     */
    public $customProperty = false;

    /**
     * Toggle searchable status for property; Determines if property is added to the query string
     * @var bool
     */
    public $searchProperty = true;

    /**
     * @var string|null
     */
    public $modelClass;

    /**
     * @return string
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * @param string $parameter
     */
    public function setParameter(string $parameter): void
    {
        $this->parameter = $parameter;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * @param string $dataType
     */
    public function setDataType(string $dataType): void
    {
        $this->dataType = $dataType;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * @return bool
     */
    public function isCustomProperty(): bool
    {
        return $this->customProperty;
    }

    /**
     * @param bool $customProperty
     */
    public function setCustomProperty(bool $customProperty): void
    {
        $this->customProperty = $customProperty;
    }

    /**
     * @return bool
     */
    public function isSearchProperty(): bool
    {
        return $this->searchProperty;
    }

    /**
     * @param bool $searchProperty
     */
    public function setSearchProperty(bool $searchProperty): void
    {
        $this->searchProperty = $searchProperty;
    }

    /**
     * @return string|null
     */
    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }

    /**
     * @param string|null $modelClass
     */
    public function setModelClass(?string $modelClass): void
    {
        $this->modelClass = $modelClass;
    }

}