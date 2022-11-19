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

declare(strict_types=1);

namespace App\Search;

class PhoneticUtility
{

    public static function getPhoneticRepresentationForWord(string $baseWord)
    {
        $metaphone = self::soundex_ger($baseWord);
        if ($metaphone === '') {
            $metaphone = 0;
        } else {
            $metaphone = hexdec(substr(md5($metaphone), 0, 7));
        }
        return $metaphone;
    }

    /**
     * Custom function for phonetic algorithm (Kölner Phonetik + number to word conversion)
     *
     * @param string $word
     * @return string Phonetic value
     */
    private static function soundex_ger(string $word): string
    {
        $code = '';
        $word = mb_strtolower($word);

        if ($word === '') {
            return '';
        }

        $replace = [
            "ç" => 'c',
            "v" => 'f',
            "w" => 'f',
            "j" => 'i',
            "y" => 'i',
            "ph" => 'f',
            "ä" => 'a',
            "ö" => 'o',
            "ü" => 'u',
            "ß" => 'ss',
            "é" => 'e',
            "è" => 'e',
            "ê" => 'e',
            "à" => 'a',
            "á" => 'a',
            "â" => 'a',
            "ë" => 'e',
        ];
        // add numbers in phonetic search (needed for product numbers)
        preg_match_all('/\d+/', $word, $matches);
        if (!empty($matches[0])) {
            $f = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            foreach ($matches[0] as $match) {
                $word = str_replace($match, $f->format((int)ltrim($match, '0')), $word);
            }
        }
        $search = array_keys($replace);
        // Replace: v->f, w->f, j->i, y->i, ph->f, ä->a, ö->o, ü->u, ß->ss, é->e, è->e, ê->e, à->a, á->a, â->a, ë->e
        $word = str_replace($search, $replace, $word);

        // Only allow letters (no numbers or special characters)
        $word = preg_replace('/[^a-zA-Z]/', '', $word);


        $wordLength = mb_strlen($word);
        $char = str_split($word);


        // Special cases for first character (Anlaut)
        if ($char[0] === 'c') {
            if ($wordLength === 1) {
                $code = 8;
                $x = 1;
            } else {
                // vor a,h,k,l,o,q,r,u,x
                switch ($char[1]) {
                    case 'a':
                    case 'h':
                    case 'k':
                    case 'l':
                    case 'o':
                    case 'q':
                    case 'r':
                    case 'u':
                    case 'x':
                        $code = "4";
                        break;
                    default:
                        $code = "8";
                        break;
                }
                $x = 1;
            }
        } else {
            $x = 0;
        }

        for (; $x < $wordLength; $x++) {

            switch ($char[$x]) {
                case 'a':
                case 'e':
                case 'i':
                case 'o':
                case 'u':
                    $code .= "0";
                    break;
                case 'b':
                case 'p':
                    $code .= "1";
                    break;
                case 'd':
                case 't':
                    if ($x + 1 < $wordLength) {
                        switch ($char[$x + 1]) {
                            case 'c':
                            case 's':
                            case 'z':
                                $code .= "8";
                                break;
                            default:
                                $code .= "2";
                                break;
                        }
                    } else {
                        $code .= "2";
                    }
                    break;
                case 'f':
                    $code .= "3";
                    break;
                case 'g':
                case 'k':
                case 'q':
                    $code .= "4";
                    break;
                case 'c':
                    if ($x + 1 < $wordLength) {
                        switch ($char[$x + 1]) {
                            case 'a':
                            case 'h':
                            case 'k':
                            case 'o':
                            case 'q':
                            case 'u':
                            case 'x':
                                switch ($char[$x - 1]) {
                                    case 's':
                                    case 'z':
                                        $code .= "8";
                                        break;
                                    default:
                                        $code .= "4";
                                }
                                break;
                            default:
                                $code .= "8";
                                break;
                        }
                    } else {
                        $code .= "4";
                    }
                    break;
                case 'x':
                    if ($x > 0) {
                        switch ($char[$x - 1]) {
                            case 'c':
                            case 'k':
                            case 'q':
                                $code .= "8";
                                break;
                            default:
                                $code .= "48";
                                break;
                        }
                    } else {
                        $code .= "48";
                    }
                    break;
                case 'l':
                    $code .= "5";
                    break;
                case 'm':
                case 'n':
                    $code .= "6";
                    break;
                case 'r':
                    $code .= "7";
                    break;
                case 's':
                case 'z':
                    $code .= "8";
                    break;
            }

        }
        $code = preg_replace("/(.)\\1+/", "\\1", $code);

        $codeLength = mb_strlen($code);
        $num = str_split($code);
        $phoneticCode = $num[0];

        for ($x = 1; $x < $codeLength; $x++) {
            if ($num[$x] !== "0") {
                $phoneticCode .= $num[$x];
            }
        }

        return $phoneticCode;
    }

}
