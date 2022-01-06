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

namespace App\Exporter\Pdf;

use setasign\Fpdi\Tcpdf\Fpdi;

class PDF extends Fpdi
{

    /**
     * Add a new empty page with an optional background template
     *
     * @param int|null $templatePageNr
     * @param $orientation (string) page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
     * @param $format (mixed) The format used for pages. It can be either: one of the string values specified at getPageSizeFromFormat() or an array of parameters specified at setPageFormat().
     * @param $keepmargins (boolean) if true overwrites the default page margins with the current margins
     */
    public function addPageWithTemplate(?int $templatePageNr = null, $orientation='', $format='', $keepmargins=false): void
    {
        $this->AddPage($orientation, $format, $keepmargins);
        if ($templatePageNr) {
            $tplIdx = $this->importPage($templatePageNr, '/MediaBox');
            $this->useTemplate($tplIdx, 0, 0, null, null, true);
        }
    }

    public function getRemainingHeight(): float
    {
        return $this->getPageHeight() - $this->getFooterMargin() - $this->GetY();
    }

    /**
     * @param string $rgb Color as RGB hex value like #AA00CC
     * @return array with colors RGB as decimal values
     */
    public function convertRGBcolor(string $rgb): array
    {
        if (preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $rgb, $matches) === 1) {
            return [
                hexdec($matches[1]),
                hexdec($matches[2]),
                hexdec($matches[3]),
            ];
        }

        throw new \InvalidArgumentException("Expected valid rgb value but got \"$rgb\"");
    }

    /**
     * Modify Y value by $value
     * @param float $value
     */
    public function addY($value): void
    {
        $this->SetY($this->GetY() + $value);
    }

    /**
     * Modify X value by $value
     * @param float $value
     */
    public function addX($value): void
    {
        $this->SetX($this->GetX() + $value);
    }
}