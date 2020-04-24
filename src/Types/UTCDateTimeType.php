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

namespace App\Types;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Doctrine DBAL UTC converted DateTime type
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
class UTCDateTimeType extends DateTimeType
{

    /**
     * UTC DateTimeZone object
     *
     * @var DateTimeZone
     */
    private static $utc = null;

    /**
     * Application DateTimeZone object
     *
     * @var DateTimeZone
     */
    private static $appTimezone = null;

    private const defaultDateTimeString = "Y-m-d H:i:s";

    /**
     * Convert the PHP value to the Database value.
     *
     * Set UTC timezone for the DateTime object.
     *
     * @param DateTime         $value
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($value, ?AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        $formatString = $platform ? $platform->getDateTimeFormatString() : self::defaultDateTimeString;

        $value->setTimezone(
            (self::$utc) ? self::$utc : (self::$utc = new DateTimeZone('UTC'))
        );

        $formatted = $value->format($formatString);

        return $formatted;
    }

    /**
     * Convert the Database value to the PHP value.
     *
     * Create a new DateTime object with UTC timezone from the database value.
     *
     * @param string           $value
     * @param AbstractPlatform $platform
     *
     * @return DateTime|mixed|null
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, ?AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $phpValue = DateTime::createFromFormat(
            $platform ? $platform->getDateTimeFormatString() : self::defaultDateTimeString,
            $value,
            (self::$utc) ? self::$utc : (self::$utc = new DateTimeZone('UTC'))
        );
        if (!$phpValue) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        // Convert to application default or user time zone
        $phpValue->setTimezone(
            (self::$appTimezone) ? self::$appTimezone
                : (self::$appTimezone = new DateTimeZone(date_default_timezone_get()))
        );

        return $phpValue;
    }
}
