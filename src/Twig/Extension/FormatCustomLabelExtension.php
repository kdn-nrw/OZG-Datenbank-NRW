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

class FormatCustomLabelExtension extends AbstractExtension
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
        $text =
            preg_replace_callback(
                '/([a-z])([A-Z])/',
                static function ($a) {
                    return $a[1] . " " . strtolower($a[2]);
                },
                $customLabel
            );
        return ucwords($text);
    }
}
