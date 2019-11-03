<?php
/**
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */

namespace App\Util;


/**
 * Class SnakeCaseConverter
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
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
