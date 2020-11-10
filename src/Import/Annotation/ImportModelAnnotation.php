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

namespace App\Import\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class ImportModelAnnotation
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 * @Required
 */
class ImportModelAnnotation extends Annotation
{
    public const DATA_TYPE_STRING = 'string';
    public const DATA_TYPE_INT = 'int';
    public const DATA_TYPE_FLOAT = 'float';
    public const DATA_TYPE_DATE = 'date';
    public const DATA_TYPE_BOOLEAN = 'boolean';
    public const DATA_TYPE_DECIMAL = 'decimal';
    public const DATA_TYPE_MODEL = 'model';
    public const DATA_TYPE_ARRAY = 'array';
    public const DATA_TYPE_COLLECTION = 'collection';
    public const DATA_TYPE_CALLBACK = 'callback';

    /**
     * @var string
     */
    public $parameter;

    /**
     * @var string
     */
    public $dataType = self::DATA_TYPE_STRING;

    /**
     * The target data type for the property; currently not used!
     *
     * @var string
     */
    public $targetDataType;

    /**
     * @var bool
     */
    public $required = false;

    /**
     * Enable auto increment for property
     *
     * @var bool
     */
    public $autoIncrement = false;

    /**
     * @var string|null
     */
    public $targetEntity;

    /**
     * @var string|null
     */
    public $mapToProperty;

    /**
     * Map the result to the given model class
     *
     * @var string|null
     */
    public $modelClass;

    /**
     * @var bool
     */
    public $disableImport = false;

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
     * @return string
     */
    public function getTargetDataType(): string
    {
        return $this->targetDataType;
    }

    /**
     * @param string $targetDataType
     */
    public function setTargetDataType(string $targetDataType): void
    {
        $this->targetDataType = $targetDataType;
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
     * @return string|null
     */
    public function getTargetEntity(): ?string
    {
        return $this->targetEntity;
    }

    /**
     * @param string|null $targetEntity
     */
    public function setTargetEntity(?string $targetEntity): void
    {
        $this->targetEntity = $targetEntity;
    }

    /**
     * @return string|null
     */
    public function getMapToProperty(): ?string
    {
        return $this->mapToProperty;
    }

    /**
     * @param string|null $mapToProperty
     */
    public function setMapToProperty(?string $mapToProperty): void
    {
        $this->mapToProperty = $mapToProperty;
    }

    /**
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * @param bool $autoIncrement
     */
    public function setAutoIncrement(bool $autoIncrement): void
    {
        $this->autoIncrement = $autoIncrement;
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

    /**
     * @return bool
     */
    public function isDisableImport(): bool
    {
        return $this->disableImport;
    }

    /**
     * @param bool $disableImport
     */
    public function setDisableImport(bool $disableImport): void
    {
        $this->disableImport = $disableImport;
    }

}