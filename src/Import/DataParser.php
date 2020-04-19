<?php
namespace App\Import;

class DataParser
{
    /**
     * Replace some characters
     *
     * @param string $value
     * @return string
     */
    public function formatString($value): string
    {
        $trgEnc = 'UTF-8';
        $cleanedValue = $value;//$this->cleanStringValue($value);
        if (function_exists('mb_detect_encoding')) {
            $enc = mb_detect_encoding($value, $trgEnc, true);
            if (empty($enc)) {
                $enc = mb_detect_encoding($value, 'ISO-8859-1', true);
            }
        }
        if (!empty($enc) && $enc !== 'UTF-8' && function_exists('iconv')) {
            $cleanedValue = iconv($enc, $trgEnc, $cleanedValue);
        }
        return $cleanedValue;//$this->cleanStringValue($cleanedValue);
    }

    public function cleanStringValue(string $value): string
    {
        $cleanVal = $value;
        $chIn  = array('ç', 'ä', 'ü',  'ö', 'Ä', 'Ü', 'Ö', 'ß', 'á', 'à',
            'â', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ó', 'ò', 'ô', 'ú', 'ù',
            'û', 'Á', 'À', 'Â', 'É', 'È', 'Ê', 'Í', 'Ì', 'Î', 'Ó', 'Ò', 'ô',
            'Ú', 'Ù', 'Û', 'ñ', '´', '`');
        $chOut = array('c', 'ae' , 'ue', 'oe', 'Ae', 'Ue', 'Oe', 'ss', 'a',
            'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'o', 'o', 'o', 'u',
            'u', 'u', 'A', 'A', 'A', 'E', 'E', 'E', 'I', 'I', 'I', 'O', 'O',
            'O', 'U', 'U', 'U', 'n', '', '');
        foreach ($chIn as $offset => $srcChar) {
            $trgChars = $chOut[$offset];
            $cleanVal = str_replace(array($srcChar, utf8_decode($srcChar)), array($trgChars, $trgChars), $cleanVal);
        }
        return $cleanVal;
    }

    /**
     * Format string to boolean value
     *
     * @param string $value
     *
     * @return boolean
     */
    public function formatBoolean($value): bool
    {

        $formattedValue = false;
        $chkVal = strtolower($value);
        if ($chkVal === 'ja' || $chkVal === 'yes' || $chkVal === '1') {
            $formattedValue = true;
        }
        return $formattedValue;
    }

    /**
     * Format csv string to array value
     *
     * @param string $value
     *
     * @return array
     */
    public function formatCsv($value): array
    {
        $possibleDelimiters = ['|', ',', ';',];
        $delimiter = ';';
        foreach ($possibleDelimiters as $checkDelimiter) {
            if (strpos($value, $checkDelimiter) !== false) {
                $delimiter = $checkDelimiter;
            }
        }
        return $value !== '' ? explode($delimiter, $value) : [];
    }

    /**
     * Parses the given string value to extract a number. The function can
     * create valid float and integer values for different input formats,
     * e.g. if "," is used for float numbers instead of ".".
     *
     * Usage: Helper::parseNumber($value);
     *
     * @param string $string A string containing a number
     *
     * @return float A valid number
     */
    public function formatFloat($string): float
    {
        if ($string === '') {
            return 0;
        }
        $value = $string;
        $commaPos = strpos($value, ',');
        $commaSet = $commaPos !== false;
        $pointPos = strpos($value, '.');
        $pointSet = $pointPos !== false;
        if ($commaSet) {
            if ($pointSet) {
                //12,345.67
                if ($pointPos > $commaPos) {
                    $value = str_replace(',', '', $value);
                //12.345,67
                } else {
                    $value = str_replace(['.', ','], ['', '.'], $value);
                }
                $value = (float)$value;
            //12345,67
            } else {
                $value = str_replace(',', '.', $value);
                $value = (float)$value;
            }
        }
        //invalid number e.g. 1a4c
        elseif (!$pointSet) {
            $value = $this->formatInt($value);
        } else {
            $value = (float)$value;
        }
        if (empty($value)) {
            $value = 0.0;
        } else {
            $value = (float) str_replace( ',', '.', $value );
        }
        return $value;
    }

    /**
     * Parses the given string value to extract an integer.
     *
     * @param string $value A string containing an integer
     *
     * @return int A valid integer
     */
    public function formatInt($value): int
    {
        $intVal = preg_replace('/[^0-9]/', '', trim($value));
        if (strlen($intVal) > 1) {
            $intVal = ltrim($intVal, '0');
        }
        //Return 0 if string is empty
        if ($intVal === '') {
            $intVal = 0;
        }
        //if value starts with minus sign, add sign to intVal (negative value)
        if (strpos($value, '-') === 0) {
            $intVal = '-' . $intVal;
        }
        return $intVal;
    }

    /**
     * Try to convert a date value into the format "YYYY-MM-DD"
     *
     * @param string $value
     * @return string|null
     */
    public function formatDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $date = $this->convertDateString($value);
        // Value is already in correct format
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }
        $year = 0;
        $month = 0;
        $day = 0;
        if (strpos($value, '.') !== false) {
            $dateParts = explode('.', $value);
            if (count($dateParts) === 3) {
                $year = (int) $dateParts[2];
                $month = (int) $dateParts[1];
                $day = (int) $dateParts[0];
            }
        } else {
            $dateParts = explode('-', $value);
            $year = (int) $dateParts[0];
            $month = (int) $dateParts[1];
            $day = (int) $dateParts[2];
        }
        $value = '';
        if ($day > 0 && $month > 0 && $year > 0) {
            if (strlen($year) === 2) {
                $year += $year > 50 ? 1900 : 2000;
            }
            if ($month < 10) {
                $month = '0' . $month;
            }
            if ($day < 10) {
                $day = '0' . $day;
            }
            $value = $year . '-' . $month . '-' . $day;
        }
        return $value;
    }

    /**
     * Attempts to convert a string into a DateTime object
     *
     * @param  string $value
     *
     * @return bool|\DateTime
     */
    protected function convertDateString($value)
    {
        $date = self::dateGerman2Iso($value);
        if (method_exists('DateTime', 'createFromFormat')) {
            $date = \DateTime::createFromFormat('Y-m-d', $date);

            // Invalid dates can show up as warnings (ie. "2007-02-99")
            // and still return a DateTime object.
            $errors = \DateTime::getLastErrors();
            if ($errors['warning_count'] > 0) {
                return false;
            }
        }

        return $date;
    }

    /**
     * Transforms a german date to a MySQL date (ISO-Date).
     *
     * @param string $checkDate Date string with format dd.mm.YYYY (or YY)
     * @return string Iso date
     */
    public static function dateGerman2Iso($checkDate)
    {
        if (empty($checkDate)) {
            return '0000-00-00';
        }
        //If date does not have format dd.mm.YYYY
        if (strpos($checkDate, '.') === false) {
            return $checkDate;
        }
        $date = $checkDate;
        [$day, $month, $year] = explode('.', $date);
        if (strlen($year) === 2) {
            $year += 2000;
        }
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
