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

use App\Translator\TranslatorAwareTrait;
use App\Entity\ModelRegion\ModelRegionProject;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConceptPdfExporter
{
    use TranslatorAwareTrait;

    /**
     * @var PDF
     */
    private $pdf;

    protected $marginLeft = 25;
    protected $fontFamily = 'helvetica';
    protected $lineStyle = ['width' => 0.25, 'cap' => 'butt', 'color' => [200, 200, 200]];
    protected $contentCol1Width = 55;

    /**
     * PDF constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
        $this->pdf = new PDF();
    }

    public function exportForProject(ModelRegionProject $project): array
    {
        $pdf = $this->pdf;
        $prefix = 'Konzeptabfrage';
        $subject = str_replace("\n", " ", $prefix . ': ' . $project->getName());
        if (mb_strlen($subject) > 1024) {
            $subject = mb_substr($subject, 0, 1024);
        }
        $pdf->SetSubject($subject);
        $templateFile = dirname(__FILE__, 4) . '/templates/ModelRegion/konzeptabfrage.pdf';
        $rootDir = dirname(__FILE__, 4);
        $targetDir = $rootDir . '/public/media/cache/pdf-export/';
        if (!is_dir($targetDir)) {
            /** @noinspection MkdirRaceConditionInspection */
            mkdir($targetDir, 0775);
        }
        $targetFileName = 'concept-' . $project->getId() . '.pdf';
        $targetFileAsbPath = $targetDir . $targetFileName;
        $filename = $project->getName();
        $filename = preg_replace("/[^0-9a-zA-Z\-_]+/", "_", $filename);
        $filename = trim(str_replace('__', '_', $filename), '_');
        if (strlen($filename) > 100) {
            $filename = substr($filename, 0, 100);
        }
        $filename = 'Konzeptabfrage-' . \App\Import\DataParser::cleanStringValue($filename) . '.pdf';
        $exportFileInfo = [
            'abs_path' => $targetFileAsbPath,
            'filename' => $filename,
        ];
        if (file_exists($targetFileAsbPath)) {
            $lastChange = max($project->getModifiedAt()->getTimestamp(), filemtime($templateFile));
            if (filemtime($targetFileAsbPath) > $lastChange) {
                return $exportFileInfo;
            }
            unlink($targetFileAsbPath);
        }
        $pdf->setSourceFile($templateFile);
        $pdf->addPageWithTemplate(1);
        $pdf->SetTextColor(0, 0, 0);
        $groupedQueries = $project->getGroupedConceptQueries();
        $marginRight = 21;
        $startX = $this->marginLeft;
        $startY = 40;
        $offsetBottom = 20;
        $pageWidth = $pdf->getPageWidth();
        $contentWidth = $pageWidth - $this->marginLeft - $marginRight;
        $pdf->SetFont($this->fontFamily, '', 11);
        $pdf->SetXY($startX, $startY);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $needsEndLine = false;
        foreach ($groupedQueries as $groupData) {
            $rowHeights = [];
            foreach ($groupData['queries'] as $offset => $row) {
                $pdf2 = clone $pdf;
                $pdf2->addPageWithTemplate(1);
                $rowHeights[$offset] = $this->writeContent($pdf2, $row, $contentWidth);
                unset($pdf2);
            }
            if ($pdf->GetY() + $rowHeights[0] > $pdf->getPageHeight() - $startY - $offsetBottom) {
                if ($needsEndLine) {
                    $y = $pdf->GetY();
                    $pdf->Line($startX, $y, $startX + $contentWidth, $y, $this->lineStyle);
                }
                $pdf->addPageWithTemplate(1);
                $pdf->SetXY($startX, $startY);
            }
            $this->writeGroupHeader($pdf, $groupData, $contentWidth);
            $lastOffset = count($groupData['queries']) - 1;
            foreach ($groupData['queries'] as $offset => $row) {
                if ($pdf->GetY() + $rowHeights[$offset] > $pdf->getPageHeight() - $startY - $offsetBottom) {
                    if ($needsEndLine) {
                        $y = $pdf->GetY();
                        $pdf->Line($startX, $y, $startX + $contentWidth, $y, $this->lineStyle);
                    }
                    $pdf->addPageWithTemplate(1);
                    $pdf->SetXY($startX, $startY);
                    if ($needsEndLine) {
                        $pdf->Line($startX, $startY, $startX + $contentWidth, $startY, $this->lineStyle);
                    }
                    //$this->writeGroupHeader($pdf, $groupData, $contentWidth);
                }
                $this->writeContent($pdf, $row, $contentWidth, $rowHeights[$offset]);
                if ($offset < $lastOffset) {
                    $y = $pdf->GetY();
                    $pdf->Line($startX, $y, $startX + $contentWidth, $y, $this->lineStyle);
                } else {
                    $needsEndLine = true;
                }
            }
        }
        if ($needsEndLine) {
            $y = $pdf->GetY();
            $pdf->Line($startX, $y, $startX + $contentWidth, $y, $this->lineStyle);
        }
        //$pdf->Output($filename, 'I');
        $pdf->Output($targetFileAsbPath, 'F');
        return $exportFileInfo;

    }

    /**
     * Write the headline of the current query group
     *
     * @param PDF $pdf
     * @param array $groupData
     * @param float $contentWidth
     * @return void
     */
    private function writeGroupHeader(PDF $pdf, array $groupData, $contentWidth)
    {
        $headerHeight = 7;
        $startX = $this->marginLeft;
        $y = $pdf->GetY();
        $pdf->SetX($startX);
        $pdf->SetFont($this->fontFamily, 'B', 11);
        $pdf->SetFillColor(0, 48, 100);
        $pdf->Rect($startX, $y, $contentWidth, $headerHeight, 'F');
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, $headerHeight, $this->translate($groupData['label']), 0, 1);
    }

    /**
     * Write the content of the given query row
     *
     * @param PDF $pdf
     * @param array $row
     * @param float $contentWidth
     * @param float $rectHeight
     * @return float|mixed
     */
    private function writeContent(PDF $pdf, array $row, $contentWidth, $rectHeight = 0)
    {
        $contentStartY = $pdf->GetY();
        $pdf->SetFont($this->fontFamily, '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(242, 242, 242);
        //$col2Width = $contentWidth - $col1Width;
        //$pdf->Rect($x, $y, $contentWidth, 10, 'F');
        $text = $row['name'];
        $isHtml = false;
        if (!empty($row['typeDescription'])) {
            $text .= '<em>(' . $row['typeDescription'] . ')</em>';
            $isHtml = true;
        }
        $startX = $this->marginLeft;
        $col1Width = $this->contentCol1Width;
        $col2Width = $contentWidth - $col1Width;
        $pdf->SetX($startX);
        $pdf->MultiCell($col1Width, $rectHeight, $text, 0, 'L', true, 1, null, null, true, 0, $isHtml);
        $pdfEndY = $pdf->GetY();
        $pdf->SetFont($this->fontFamily, '', 11);
        $col2X = $startX + $col1Width;
        $pdf->SetXY($col2X, $contentStartY);
        $pdf->MultiCell($col2Width, 0, $row['description'], 0, 'L', false);
        $pdfEndY = max($pdfEndY, $pdf->GetY());
        $pdf->SetY($pdfEndY);
        $y = $pdfEndY;
        $pdf->Line($startX, $contentStartY, $startX, $y, $this->lineStyle);
        $pdf->Line($col2X, $contentStartY, $col2X, $y, $this->lineStyle);
        $xRight = $startX + $contentWidth - .25;
        $pdf->Line($xRight, $contentStartY, $xRight, $y, $this->lineStyle);
        return $pdfEndY - $contentStartY;
    }
}