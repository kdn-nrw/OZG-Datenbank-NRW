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

namespace App\Exporter\Writer;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Style;

final class ExcelStyle
{
    /**
     * @var Style
     */
    private $style;

    /**
     * Old colors from spreadsheet excel writer
     * @var array
     */
    private $palette = array(
        array(0x00, 0x00, 0x00, 0x00),   // 8
        array(0xff, 0xff, 0xff, 0x00),   // 9
        array(0xff, 0x00, 0x00, 0x00),   // 10
        array(0x00, 0xff, 0x00, 0x00),   // 11
        array(0x00, 0x00, 0xff, 0x00),   // 12
        array(0xff, 0xff, 0x00, 0x00),   // 13
        array(0xff, 0x00, 0xff, 0x00),   // 14
        array(0x00, 0xff, 0xff, 0x00),   // 15
        array(0x80, 0x00, 0x00, 0x00),   // 16
        array(0x00, 0x80, 0x00, 0x00),   // 17
        array(0x00, 0x00, 0x80, 0x00),   // 18
        array(0x80, 0x80, 0x00, 0x00),   // 19
        array(0x80, 0x00, 0x80, 0x00),   // 20
        array(0x00, 0x80, 0x80, 0x00),   // 21
        array(0xc0, 0xc0, 0xc0, 0x00),   // 22
        array(0x80, 0x80, 0x80, 0x00),   // 23
        array(0x99, 0x99, 0xff, 0x00),   // 24
        array(0x99, 0x33, 0x66, 0x00),   // 25
        array(0xff, 0xff, 0xcc, 0x00),   // 26
        array(0xcc, 0xff, 0xff, 0x00),   // 27
        array(0x66, 0x00, 0x66, 0x00),   // 28
        array(0xff, 0x80, 0x80, 0x00),   // 29
        array(0x00, 0x66, 0xcc, 0x00),   // 30
        array(0xcc, 0xcc, 0xff, 0x00),   // 31
        array(0x00, 0x00, 0x80, 0x00),   // 32
        array(0xff, 0x00, 0xff, 0x00),   // 33
        array(0xff, 0xff, 0x00, 0x00),   // 34
        array(0x00, 0xff, 0xff, 0x00),   // 35
        array(0x80, 0x00, 0x80, 0x00),   // 36
        array(0x80, 0x00, 0x00, 0x00),   // 37
        array(0x00, 0x80, 0x80, 0x00),   // 38
        array(0x00, 0x00, 0xff, 0x00),   // 39
        array(0x00, 0xcc, 0xff, 0x00),   // 40
        array(0xcc, 0xff, 0xff, 0x00),   // 41
        array(0xcc, 0xff, 0xcc, 0x00),   // 42
        array(0xff, 0xff, 0x99, 0x00),   // 43
        array(0x99, 0xcc, 0xff, 0x00),   // 44
        array(0xff, 0x99, 0xcc, 0x00),   // 45
        array(0xcc, 0x99, 0xff, 0x00),   // 46
        array(0xff, 0xcc, 0x99, 0x00),   // 47
        array(0x33, 0x66, 0xff, 0x00),   // 48
        array(0x33, 0xcc, 0xcc, 0x00),   // 49
        array(0x99, 0xcc, 0x00, 0x00),   // 50
        array(0xff, 0xcc, 0x00, 0x00),   // 51
        array(0xff, 0x99, 0x00, 0x00),   // 52
        array(0xff, 0x66, 0x00, 0x00),   // 53
        array(0x66, 0x66, 0x99, 0x00),   // 54
        array(0x96, 0x96, 0x96, 0x00),   // 55
        array(0x00, 0x33, 0x66, 0x00),   // 56
        array(0x33, 0x99, 0x66, 0x00),   // 57
        array(0x00, 0x33, 0x00, 0x00),   // 58
        array(0x33, 0x33, 0x00, 0x00),   // 59
        array(0x99, 0x33, 0x00, 0x00),   // 60
        array(0x99, 0x33, 0x66, 0x00),   // 61
        array(0x33, 0x33, 0x99, 0x00),   // 62
        array(0x33, 0x33, 0x33, 0x00),   // 63
    );

    public function __construct(Style $style)
    {
        $this->style = $style;
    }

    /**
     * Set the style attributes for the format instance by the options array
     *
     * @param array $options
     */
    public function setFormatStylesByOptions(array $options): void
    {
        $style = $this->style;
        // map deprecated options and color values
        $this->mapColor($options, 'color', 'color');
        $this->mapColor($options, 'background-color', 'background-color');
        if (isset($options['foreground-color'])) {
            $this->mapColor($options, 'foreground-color', 'color');
            unset($options['foreground-color']);
        }
        $styleOptions = array();
        $mapOptions = ['font', 'borders', 'quotePrefix', 'alignment', 'numberformat', 'protection', 'fill'];
        foreach ($mapOptions as $key => $values) {
            if (isset($options[$key])) {
                $styleOptions[$key] = $values;
                unset($options[$key]);
            }
        }
        $mapStyleOptions = [
            'font' => array(
                'name' => 'font-family',
                'bold' => 'font-weight:bold',
                'italic' => 'font-style:italic',
                'underline' => 'text-decoration:underline:' . \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE,
                //'strike'    => false,
                'color' => array(
                    'rgb' => 'color'
                )
            ),
            'borders' => array(
                'outline' => array(
                    'borderStyle' => 'border:1:' . Border::BORDER_HAIR
                        . '|border:2:' . Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'border-color'
                    )
                ),
                'bottom' => array(
                    'borderStyle' => 'border-bottom:1:' . Border::BORDER_HAIR
                        . '|border-bottom:2:' . Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'border-bottom-color'
                    )
                ),
                'top' => array(
                    'borderStyle' => 'border-top:1:' . Border::BORDER_HAIR
                        . '|border-top:2:' . Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'border-top-color'
                    )
                ),
                'left' => array(
                    'borderStyle' => 'border-left:1:' . Border::BORDER_HAIR
                        . '|border-left:2:' . Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'border-left-color'
                    )
                ),
                'right' => array(
                    'borderStyle' => 'border-right:1:' . Border::BORDER_HAIR
                        . '|border-right:2:' . Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'border-right-color'
                    )
                ),
            ),
            'alignment' => array(
                'horizontal' => 'align',
                'vertical' => 'vertical-align',
                //'rotation' => 0,
                'wrap' => 'text-wrap:1'
                //'shrinkToFit' => '',
                //'indent' => '',
            ),
            'fill' => array(
                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => 'background-color'
                )
            ),
        ];
        $this->mapStyles($mapStyleOptions, $options, $styleOptions);
        $style->applyFromArray($styleOptions);
    }

    /**
     * Convert style options to internal format
     *
     * @param array $mapStyleOptions
     * @param array $options
     * @param array $styleOptions
     */
    private function mapStyles(array $mapStyleOptions, array $options, array &$styleOptions): void
    {
        foreach ($mapStyleOptions as $group => $groupProperties) {
            if (is_array($groupProperties)) {
                if (!isset($styleOptions[$group])) {
                    $styleOptions[$group] = array();
                }
                $subStyleOptions =& $styleOptions[$group];
                $this->mapStyles($groupProperties, $options, $subStyleOptions);
                // remove empty style groups if no child values have been set
                if (empty($styleOptions[$group])) {
                    unset($styleOptions[$group]);
                }
            } else {
                $mappingGroups = explode('|', $groupProperties);
                $isOptionSplit = count($mappingGroups) > 1;
                foreach ($mappingGroups as $mappingGroupString) {
                    $mapping = explode(':', $mappingGroupString);
                    $mapField = $mapping[0];
                    if (!empty($options[$mapField])) {
                        if (array_key_exists(1, $mapping)) {
                            if (array_key_exists(2, $mapping)) {
                                if ($isOptionSplit) {
                                    if ($options[$mapField] == $mapping[1]) {
                                        $styleOptions[$group] = $mapping[2];
                                        break;
                                    }
                                } else {
                                    $styleOptions[$group] = $mapping[2];
                                }
                            } else {
                                $styleOptions[$group] = $options[$mapField] == $mapping[1];
                            }
                        } else {
                            $styleOptions[$group] = $options[$mapField];
                        }
                    }
                }
            }
        }
    }

    /**
     * Map rgb color to internal color format
     * @param array $options
     * @param string $sourceField
     * @param string $targetField
     */
    private function mapColor(array &$options, string $sourceField, string $targetField): void
    {
        if (isset($options[$sourceField])) {
            if (!is_numeric($options[$sourceField]) && isset($this->palette[$options[$sourceField]])) {
                $colorOptions = $this->palette[$options[$sourceField]];
                $options[$targetField] = base_convert($colorOptions[0], 16, 10)
                    . base_convert($colorOptions[1], 16, 10)
                    . base_convert($colorOptions[2], 16, 10);
            } elseif (strpos($options[$sourceField], '#') === 0) {
                $options[$targetField] = ltrim($options[$sourceField], '#');
            } elseif ($targetField !== $sourceField) {
                $options[$targetField] = $options[$sourceField];
            }
        }
    }
}
