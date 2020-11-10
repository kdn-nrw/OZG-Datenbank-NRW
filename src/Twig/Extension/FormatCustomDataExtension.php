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

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatCustomDataExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFilters()
    {
        return [
            new TwigFilter('app_format_custom_label', [$this, 'getFormattedLabel']),
            new TwigFilter('app_format_custom_value', [$this, 'getFormattedValue']),
        ];
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
     * Returns the formatted value
     *
     * @param mixed $value
     * @param bool $striptags
     * @return string
     */
    public function getFormattedValue($value, bool $striptags = true): string
    {
        if (is_iterable($value)) {
            $flatValues = [];
            foreach ($value as $subVal) {
                $flatValues[] = $this->getFormattedValue($subVal, $striptags);
            }
            $displayValue = implode(', ', $flatValues);
        } else {
            $displayValue = (string) $value;
        }
        if ($striptags) {
            $displayValue = strip_tags($displayValue);
        }
        return $displayValue;
    }

    /**
     * Returns the field description collection for the referenced fields
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
}
