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
    public function classNameToSnakeCase(string $className): string
    {
        return str_replace(
            '\\',
            '.',
            strtolower(
                preg_replace_callback(
                    '/([a-z])([A-Z])/',
                    function ($a) {
                        return $a[1]."_".strtolower($a[2]);
                    },
                    lcfirst($className)
                )
            )
        );
    }
}
