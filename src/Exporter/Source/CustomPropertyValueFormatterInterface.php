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

namespace App\Exporter\Source;

interface CustomPropertyValueFormatterInterface
{
    /**
     * Returns the property value for the given object or array
     *
     * @param string $propertyPath
     * @param object|array $objectOrArray
     * @return string|null
     */
    public function getPropertyValue(string $propertyPath, $objectOrArray): ?string;
}