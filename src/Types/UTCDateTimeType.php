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
use DateTimeInterface;
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
    private static $utc;

    /**
     * Application DateTimeZone object
     *
     * @var DateTimeZone
     */
    private static $appTimezone;

    /**
     * Convert the PHP value to the Database value.
     *
     * Set UTC timezone for the DateTime object.
     *
     * @param DateTime         $value
     * @param AbstractPlatform $platform
     *
     * @return string
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }


        if ($value instanceof DateTimeInterface) {
            $value->setTimezone(
                (self::$utc) ?: (self::$utc = new DateTimeZone('UTC'))
            );
            return $value->format($platform->getDateTimeFormatString());
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
    }

    /**
     * Convert the Database value to the PHP value.
     *
     * Create a new DateTime object with UTC timezone from the database value.
     *
     * @param string           $value
     * @param AbstractPlatform $platform
     *
     * @return DateTime|DateTimeInterface|null
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            $val = clone $value;
        } else {
            $val = DateTime::createFromFormat(
                $platform->getDateTimeFormatString(),
                $value,
                (self::$utc) ?: (self::$utc = new DateTimeZone('UTC'))
            );

            if (! $val) {
                throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
            }
        }

        if (null === self::$appTimezone) {

            $timezoneText = date_default_timezone_get() ;
            if (!$timezoneText) {
                $timezoneText = 'Europe/Berlin';
            }
            self::$appTimezone = new DateTimeZone($timezoneText);
        }
        // Convert to application default or user time zone
        $val->setTimezone(self::$appTimezone);

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
