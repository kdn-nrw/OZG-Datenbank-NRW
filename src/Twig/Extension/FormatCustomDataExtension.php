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

namespace App\Twig\Extension;

use App\Entity\Base\CustomEntityLabelInterface;
use App\Translator\TranslatorAwareTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatCustomDataExtension extends AbstractExtension
{
    use TranslatorAwareTrait;

    /**
     * Registry for property paths; set if property is readable by property accessor
     * @var string[]
     */
    protected $attributeRegistry = [];

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFilters()
    {
        return [
            new TwigFilter('app_format_custom_label', [$this, 'getFormattedLabel']),
            new TwigFilter('app_format_identifier', [$this, 'getFormattedIdentifier']),
            new TwigFilter('app_format_custom_value', [$this, 'getFormattedValue']),
            new TwigFilter('app_format_property_name', [$this, 'convertToPropertyName']),
            new TwigFilter('app_format_collection_item_label', [$this, 'getCollectionItemLabel']),
            new TwigFilter('app_attribute_recursive', [$this, 'getAttributeRecursive']),
        ];
    }

    /**
     * Returns the field name converted to a property name (lower camel case)
     *
     * @param string $input
     * @return string
     */
    public function convertToPropertyName(string $input): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_', ' '], ' ', $input))));
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param string $customLabel
     * @return string
     */
    public function getFormattedLabel(string $customLabel): string
    {
        return self::convertKeyToLabel($customLabel);
    }

    /**
     * Formats the given custom key to an identifier string (lowercase, words separated by -)
     *
     * @param string $customKey
     * @return string
     */
    public function getFormattedIdentifier(string $customKey): string
    {
        return strtolower(str_replace(' ', '-', self::convertKeyToLabel($customKey)));
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param object|null $data
     * @return string
     */
    public function getCollectionItemLabel($data): string
    {
        if ($data instanceof CustomEntityLabelInterface) {
            $label = $this->translate($data->getLabelKey());
            if (method_exists($data, 'getTypeLabelKey') && $typeLabelKey = $data->getTypeLabelKey()) {
                $label = $this->translate($typeLabelKey) . ': ' . $label;
            }
            return $label;
        }
        if (null !== $data) {
            return $data . '';
        }
        return $this->translate('app.common.add_sub_record');
    }

    /**
     * Returns the formatted value
     *
     * @param mixed $value
     * @param bool $stripTags
     * @param string|null $emptyValue
     * @return string
     */
    public function getFormattedValue($value, bool $stripTags = true, ?string $emptyValue = null): string
    {
        if (is_iterable($value)) {
            $flatValues = [];
            foreach ($value as $subVal) {
                $flatValues[] = $this->getFormattedValue($subVal, $stripTags);
            }
            $displayValue = implode(', ', $flatValues);
        } elseif ($value instanceof \DateTime) {
            $displayValue = str_replace(' 00:00:00', '', date('d.m.Y H:i:s', $value->format('U')));
        } else {
            $displayValue = (string)$value;
        }
        if ($stripTags) {
            $displayValue = strip_tags($displayValue);
        }
        if (null !== $emptyValue && $displayValue === '') {
            $displayValue = $emptyValue;
        }
        return $displayValue;
    }

    /**
     * Converts a custom label to readable text
     *
     * @param string $customLabel
     * @return string
     */
    public static function convertKeyToLabel(string $customLabel): string
    {
        $tmpParts = explode('.', $customLabel);
        $text =
            preg_replace_callback(
                '/([a-z])([A-Z])/',
                static function ($a) {
                    return $a[1] . " " . strtolower($a[2]);
                },
                array_pop($tmpParts)
            );
        return ucwords($text);
    }

    /**
     * @param object|array $objectOrArray The object or array to traverse
     * @param string|PropertyPathInterface $propertyPath The property path to read
     * @return mixed|null
     */
    public function getAttributeRecursive($objectOrArray, $propertyPath)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if (isset($this->attributeRegistry[$propertyPath])) {
            return $propertyAccessor->getValue($objectOrArray, $this->attributeRegistry[$propertyPath]);
        }
        $processedPropertyPath = $this->convertToPropertyName($propertyPath);
        if ($propertyAccessor->isReadable($objectOrArray, $processedPropertyPath)) {
            $this->attributeRegistry[$propertyPath] = $processedPropertyPath;
            return $propertyAccessor->getValue($objectOrArray, $processedPropertyPath);
        }
        $firstDotPos = strpos($processedPropertyPath, '.');
        if ($firstDotPos !== false) {
            $firstProperty = trim(substr($processedPropertyPath, 0, $firstDotPos), ' .');
            $restProperty = trim(substr($processedPropertyPath, $firstDotPos), ' .');
            if ($propertyAccessor->isReadable($objectOrArray, $firstProperty)) {
                $subObjectOrArray = $propertyAccessor->getValue($objectOrArray, $firstProperty);
                if (null !== $subObjectOrArray) {
                    return $this->getAttributeRecursive($subObjectOrArray, $restProperty);
                }
            }
        }
        return null;
    }
}
