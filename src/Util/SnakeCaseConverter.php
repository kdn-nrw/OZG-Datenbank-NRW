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

namespace App\Util;


/**
 * Class SnakeCaseConverter
 */
class SnakeCaseConverter
{
    public static function camelCaseToSnakeCase(string $propertyName): string
    {
        return
            strtolower(
                preg_replace_callback(
                    '/([a-z])([A-Z])/',
                    static function ($a) {
                        return $a[1] . "_" . strtolower($a[2]);
                    },
                    lcfirst($propertyName)
                )
            );
    }

    public static function classNameToSnakeCase(string $className): string
    {
        return str_replace(
            '\\',
            '.',
            self::camelCaseToSnakeCase($className)
        );
    }
}
