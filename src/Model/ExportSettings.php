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

namespace App\Model;

use App\Exporter\Source\CustomPropertyValueFormatterInterface;
use App\Service\ApplicationContextHandler;

class ExportSettings
{
    /**
     * The export fields
     *
     * @var array|string[]
     */
    private $processedPropertyMap = [];

    /**
     * Additional export fields
     * @var array|string[]
     */
    private $additionFields = [];

    /**
     * Custom field labels
     *
     * @var array|string[]
     */
    private $customLabels = [];

    /**
     * Exclude export fields
     * @var array|string[]
     */
    private $excludeFields = ['hidden', 'slug', 'importId', 'importSource'];

    /**
     * Export formats
     *
     * @var array|string[]
     */
    private $formats = ['xlsx'];

    /**
     * The format for DateTime property values
     *
     * @var string
     */
    private $dateTimeFormat = 'd.m.Y H:i:s';

    /**
     * The application context used for the export
     *
     * @var string
     */
    private $context = ApplicationContextHandler::APP_CONTEXT_FE;

    /**
     * Start export with these fields
     *
     * @var array|string[]
     */
    private $fieldsStart = ['id', 'createdAt', 'modifiedAt', 'createdBy'];

    /**
     * Custom property value formatters
     *
     * @var array|CustomPropertyValueFormatterInterface[]
     */
    private $customPropertyValueFormatters;

    /**
     * @return array|string[]
     */
    public function getAdditionFields(): array
    {
        return $this->additionFields;
    }

    /**
     * @param array|string[] $additionFields
     */
    public function setAdditionFields(array $additionFields): void
    {
        $this->additionFields = $additionFields;
    }

    /**
     * @return array|string[]
     */
    public function getExcludeFields(): array
    {
        return $this->excludeFields;
    }

    /**
     * @param array|string[] $excludeFields
     */
    public function setExcludeFields(array $excludeFields): void
    {
        $this->excludeFields = $excludeFields;
    }

    /**
     * @param array|string[] $excludeFields
     */
    public function addExcludeFields(array $excludeFields): void
    {
        foreach ($excludeFields as $field) {
            if (!in_array($field, $this->excludeFields, false)) {
                $this->excludeFields[] = $field;
            }
        }
    }

    /**
     * @return array|string[]
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * @param array|string[] $formats
     */
    public function setFormats(array $formats): void
    {
        $this->formats = $formats;
    }

    /**
     * @return array|string[]
     */
    public function getFieldsStart()
    {
        return $this->fieldsStart;
    }

    /**
     * @param array|string[] $fieldsStart
     */
    public function setFieldsStart($fieldsStart): void
    {
        $this->fieldsStart = $fieldsStart;
    }

    /**
     * Returns the custom property label if set
     * @param string $property The property name
     * @return string|null
     */
    public function getCustomLabel(string $property): ?string
    {
        return $this->customLabels[$property] ?? null;
    }

    /**
     * Adds a custom property label
     * @param string $property The property name
     * @param string $labelKey The label key
     */
    public function addCustomLabel(string $property, string $labelKey): void
    {
        $this->customLabels[$property] = $labelKey;
    }

    /**
     * @return string
     */
    public function getDateTimeFormat(): string
    {
        return $this->dateTimeFormat;
    }

    /**
     * @param string $dateTimeFormat
     */
    public function setDateTimeFormat(string $dateTimeFormat): void
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    /**
     * @return array|string[]
     */
    public function getProcessedPropertyMap(): array
    {
        return $this->processedPropertyMap;
    }

    /**
     * @param array|string[] $processedPropertyMap
     */
    public function setProcessedPropertyMap($processedPropertyMap): void
    {
        $this->processedPropertyMap = $processedPropertyMap;
    }

    /**
     * Adds a custom property value formatter
     * @param string $property The property name
     * @param CustomPropertyValueFormatterInterface $formatter The custom formatter
     */
    public function addCustomPropertyValueFormatter(string $property, CustomPropertyValueFormatterInterface $formatter): void
    {
        $this->customPropertyValueFormatters[$property] = $formatter;
    }

    public function getCustomPropertyValueFormatter(string $propertyPath): ?CustomPropertyValueFormatterInterface
    {
        return $this->customPropertyValueFormatters[$propertyPath] ?? null;
    }
}
