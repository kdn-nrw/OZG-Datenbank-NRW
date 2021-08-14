<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

/**
 * Custom model for excel export values
 *
 * @package App\Model
 */
class ExportCellValue
{
    public const VALUE_TYPE_STRING = 'string';
    public const VALUE_TYPE_INT = 'int';
    public const VALUE_TYPE_FLOAT = 'float';
    public const VALUE_TYPE_DATE = 'date';
    public const VALUE_TYPE_DATE_TIME = 'datetime';
    public const VALUE_TYPE_BOOLEAN = 'boolean';
    public const VALUE_TYPE_DECIMAL = 'decimal';
    public const VALUE_TYPE_FORMULA = 'formula';


    /**
     * The format for DateTime property values
     *
     * @var string
     */
    private const DATE_FORMAT = 'd.m.Y H:i:s';
    private const DATE_PARTS = [
        'y' => 'Y',
        'm' => 'M',
        'd' => 'D',
    ];
    private const TIME_PARTS = [
        'h' => 'H',
        'i' => 'M',
        's' => 'S',
    ];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;
    /**
     * @var string
     */
    protected $valueType = self::VALUE_TYPE_STRING;

    /**
     * @var string|null
     */
    protected $url;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * ExportCellValue constructor.
     * @param string $name
     * @param mixed $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getValueType(): string
    {
        return $this->valueType;
    }

    /**
     * @param string $valueType
     */
    public function setValueType(string $valueType): void
    {
        $this->valueType = $valueType;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return array|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @param array|null $options
     */
    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }

    public function __toString(): string
    {
        return self::formatValue($this->value);
    }

    public function __serialize(): array
    {
        return [$this->value, $this->name, $this->url, $this->options];
    }

    public function __unserialize(array $data): void
    {
        [$this->value, $this->name, $this->url, $this->options] = $data;
    }

    /**
     * Format the given value
     *
     * @param mixed $value
     * @param string $dateFormat
     * @return string
     */
    public static function formatValue($value, string $dateFormat = self::DATE_FORMAT): string
    {
        //if value is array or collection, creates string
        if (\is_iterable($value)) {
            $result = array();
            foreach ($value as $item) {
                $result[] = self::formatValue($item, $dateFormat);
            }
            $value = implode(', ', $result);
        } elseif ($value instanceof \DateTimeInterface) {
            $value = $value->format($dateFormat);
        } elseif ($value instanceof \DateInterval) {
            $value = self::getDuration($value);
        } elseif (\is_object($value)) {
            $value = (string)$value;
        } elseif (is_string($value)) {
            $value = trim(strip_tags($value));
        }

        return (string)$value;
    }

    /**
     * @param \DateInterval $interval
     * @return string An ISO8601 duration
     */
    private static function getDuration(\DateInterval $interval): string
    {
        $datePart = '';
        foreach (self::DATE_PARTS as $datePartAttribute => $datePartAttributeString) {
            if ($interval->$datePartAttribute !== 0) {
                $datePart .= $interval->$datePartAttribute . $datePartAttributeString;
            }
        }

        $timePart = '';
        foreach (self::TIME_PARTS as $timePartAttribute => $timePartAttributeString) {
            if ($interval->$timePartAttribute !== 0) {
                $timePart .= $interval->$timePartAttribute . $timePartAttributeString;
            }
        }

        if ('' === $datePart && '' === $timePart) {
            return 'P0Y';
        }

        return 'P' . $datePart . ('' !== $timePart ? 'T' . $timePart : '');
    }


}
