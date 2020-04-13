<?php
/**
 * Mindbase 3
 *
 * PHP version 7.2
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2018 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      http://www.brain-appeal.com/
 * @since     2018-01-24
 */

namespace App\Statistics;

use App\Statistics\Event\ExportWriterEvent;
use App\Statistics\Options\ExportOptions;
use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Excel writer for statistics providers
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2018 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      http://www.brain-appeal.com/
 * @since     2018-01-24
 */
class ExcelWriter extends Spreadsheet
{
    /**
     * Storage for excel Options
     *
     * @var ExportOptions
     */
    private $excelOptions;

    /**
     * Mapping Array for columns
     *
     * @var array
     */
    private $columnNameMapping;

    /**
     * @var ExportHelper
     */
    private $exportHelper;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Constructor
     *
     * @param ExportHelper $exportHelper
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ExportHelper $exportHelper,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
        $this->exportHelper = $exportHelper;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Rewrite the execution limits
     * @param $executionTime
     * @param $memoryLimit
     * @param string $memoryLimitUnit
     */
    protected function rewriteExecutionLimits($executionTime, $memoryLimit, $memoryLimitUnit = 'M')
    {
        ini_set('max_execution_time', $executionTime);
        $mem = trim(ini_get("memory_limit"), $memoryLimitUnit);
        if ($mem < $memoryLimit) {
            ini_set('memory_limit', $memoryLimit . $memoryLimitUnit);
        }

    }

    /**
     * The main function to do excel exports
     *
     * @param string $fileName
     * @param array $data
     * @param ExportOptions $options
     * @param string $sheetName
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export($fileName, array $data, ExportOptions $options, $sheetName = 'Export')
    {
        $this->rewriteExecutionLimits(900, 512);
        $this->excelOptions = $options;

        $headers = $this->exportHelper->generateHeaders($options);

        $this->createFile($sheetName, $data, $headers);
        $this->sendFile($fileName);
    }

    /**
     * File creation is done here
     *
     * @param string $sheetName
     * @param array $data
     * @param array $headers
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function createFile($sheetName, array $data, array $headers)
    {
        $properties = $this->getProperties();

        $metaData = $this->excelOptions->getFileMetaData();
        foreach ($metaData as $key => $value) {
            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($properties, $setter)) {
                $properties->$setter($value);
            }
        }

        $sheet = $this->initializeSheet($sheetName);

        $this->columnNameMapping = [];
        for ($column = 0, $hCount = count($headers); $column < $hCount; $column++) {
            $this->columnNameMapping[] = Coordinate::stringFromColumnIndex($column);
        }

        $columnWidths = [];

        $this->writeHeadersToFile($sheet, $headers, $columnWidths);
        $this->writeDataToFile($sheet, $data, $columnWidths);

        $this->applyFormats($columnWidths);

        $event = new ExportWriterEvent($this);
        $this->dispatchEvent($event, 'created');

    }

    /**
     * Send the created file to php output
     *
     * @param string $fileName
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function sendFile($fileName)
    {
        $fileExtension = strtolower(substr(strrchr($fileName, '.'), 1));
        $writerType = 'Xlsx';
        if ($fileExtension === 'xlsx') {
            // Redirect output to a client's web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif ($fileExtension === 'pdf') {
            header('Content-Type: application/pdf');
            $writerType = 'Mpdf';
        }
        header('Content-Disposition: attachment;filename="' . basename($fileName) . '"');
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($this, $writerType);
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Return an empty worksheet with default settings
     *
     * @param string $sheetName
     * @return Worksheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function initializeSheet($sheetName)
    {
        $sheet = $this->setActiveSheetIndex(0);
        $sheet->setTitle($sheetName);
        //$sheet->setShowGridlines(false);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_DEFAULT);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);

        $style = $this->getDefaultStyle();
        $font = $style->getFont();
        $font->setName('Arial');
        $font->setSize(10);
        Cell::setValueBinder(new AdvancedValueBinder());

        $pageMargins = $sheet->getPageMargins();
        $pageMargins->setTop(0.4);
        $pageMargins->setRight(0.4);
        $pageMargins->setLeft(0.4);
        $pageMargins->setBottom(0.4);

        return $sheet;
    }

    /**
     * Write the given headers into output file
     * @param Worksheet $sheet
     * @param array $hdCols
     * @param array $columnWidths
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function writeHeadersToFile(Worksheet $sheet, $hdCols, &$columnWidths)
    {
        $minColumnWidth = $this->excelOptions->getColumnWidthMin();
        $maxColumnWidth = $this->excelOptions->getColumnWidthMax();

        // Add column headers
        $setBold = true;
        $rowNr = 1;
        foreach ($hdCols as $offset => $hdTxt) {
            $colName = Coordinate::stringFromColumnIndex($offset);
            $cellId = $colName . $rowNr;
            $sheet->setCellValue($cellId, $hdTxt);
            $this->setCellFont($cellId, 10, $setBold, 'center', 'center');
            $this->setCellBorder($cellId, 'left', 'CCCCCC');
            $this->enableTextWrap($cellId);
            $columnWidths[$colName] = max($minColumnWidth, min($maxColumnWidth, mb_strwidth($hdTxt)));
        }

        $firstChar = Coordinate::stringFromColumnIndex(0);
        $lastChar = Coordinate::stringFromColumnIndex(count($hdCols) - 1);
        $cellsBrdOutline = $firstChar . $rowNr . ':' . $lastChar . $rowNr;
        $this->setCellBorder($cellsBrdOutline, 'outline', '000000');
        $this->setRowHeight(1, 18);
    }

    /**
     * Write the given data into output file
     * @param Worksheet $sheet
     * @param array $data
     * @param array $columnWidths
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function writeDataToFile(Worksheet $sheet, array $data, array &$columnWidths)
    {
        $minColumnWidth = $this->excelOptions->getColumnWidthMin();
        $maxColumnWidth = $this->excelOptions->getColumnWidthMax();

        $dataStartRow = 2;
        $minRows = 8;

        $rowCount = count($data);
        $expRows = max($rowCount, $minRows);

        for ($i = 0; $i < $expRows; $i++) {
            $rowNr = $dataStartRow + $i;
            if (isset($data[$i])) {
                $record = &$data[$i];
                $recFields = array_keys($record);
                foreach ($this->columnNameMapping as $colIndex => $colName) {
                    $cellNr = $colName . $rowNr;
                    $cellVal = $record[$recFields[$colIndex]];
                    $this->setCellBorder($cellNr, 'left', 'CCCCCC');
                    $this->setCellBorder($cellNr, 'right', 'CCCCCC');
                    $this->setCellBorder($cellNr, 'bottom', 'CCCCCC');
//                    $this->setNumberFormat($cellNr, $strFormat);
//                    $sheet->setCellValue($cellNr, $cellVal);
                    $sheet->setCellValueExplicit($cellNr, $cellVal, DataType::TYPE_STRING);
                    $this->setCellFont($cellNr, 10, false, 'left', 'top');
                    $this->enableTextWrap($cellNr);

                    // calculate needed cell width
                    $columnWidths[$colName] = max(
                        $columnWidths[$colName],
                        $minColumnWidth,
                        min($maxColumnWidth, mb_strwidth($cellVal))
                    );
                }

            } else {
                foreach ($this->columnNameMapping as $colName) {
                    $cellNr = $colName . $rowNr;
                    $this->setCellBorder($cellNr, 'left', 'CCCCCC');
                    $this->setCellBorder($cellNr, 'right', 'CCCCCC');
                    $this->setCellBorder($cellNr, 'bottom', 'CCCCCC');
                }
            }
        }
    }

    /**
     * Apply column formats
     * @param float[] $columnWidths
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function applyFormats($columnWidths)
    {
        foreach ($columnWidths as $colId => $colWidth) {
            $this->setColWidth($colId, $colWidth);
        }

//        $nrCommaFormat = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
//        $eurFormat = '_-* #,##0.00 €_-;-* #,##0.00 €_-;_-* "-"?? €_-;_-@_-';
//        $nrFormat = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER;
//        $dateFormat = 'dd.mm.yy';
//
//        foreach ($dateCols as $col) {
//            $cellSpan = $col . $dataStartRow . ':' . $col . $rowNr;
//            $this->setNumberFormat($cellSpan, $dateFormat);
//        }
//        foreach ($nrCols as $col) {
//            $cellSpan = $col . $dataStartRow . ':' . $col . $rowNr;
//            $this->setNumberFormat($cellSpan, $nrCommaFormat);
//        }
//        foreach ($eurCols as $col) {
//            $cellSpan = $col . $dataStartRow . ':' . $col . $rowNr;
//            $this->setNumberFormat($cellSpan, $eurFormat);
//        }

    }

    /**
     * @param string $cellCoordinate Cell coordinate (or range) to get style for, eg: 'A1'
     * @param float $fontSize New font size
     * @param bool $setBold (optional) set bold
     * @param string $hAlign (optional) set horizontal text alignment; default: left
     * @param string $vAlign (optional) set vertical text alignment; default: top
     * @param bool $underline (optional) set text decoration: underline
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function setCellFont(
        $cellCoordinate,
        $fontSize,
        $setBold = false,
        $hAlign = 'left',
        $vAlign = 'top',
        $underline = false
    ) {
        $style = $this->getActiveSheet()->getStyle($cellCoordinate);
        $style->getFont()->setSize($fontSize)->setBold($setBold);
        $style->getAlignment()->setHorizontal($hAlign);
        $style->getAlignment()->setVertical($vAlign);
        if ($underline) {
            $style->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
        }
    }

    /**
     * @param string $cellCoordinate Cell coordinate (or range) to get style for, eg: 'A1'
     * @param string $format see NumberFormat::FORMAT_*
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function setNumberFormat(
        $cellCoordinate,
        $format = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
    ) {
        $style = $this->getActiveSheet()->getStyle($cellCoordinate);
        $style->getNumberFormat()->setFormatCode($format);
    }

    /**
     * @param string $colName String index of the column eg: 'A'
     * @param float $colWidth Column width
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function setColWidth($colName, $colWidth)
    {
        $this->getActiveSheet()->getColumnDimension($colName)->setWidth($colWidth);
    }

    /**
     * @param int $rowNr Numeric index of the row
     * @param float $rowHeight Row height
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function setRowHeight($rowNr, $rowHeight)
    {
        $this->getActiveSheet()->getRowDimension($rowNr)->setRowHeight($rowHeight);
    }

    /**
     * @param string $cellCoordinate Cell coordinate (or range) to get style for, eg: 'A1'
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function enableTextWrap($cellCoordinate)
    {
        $this->getActiveSheet()->getStyle($cellCoordinate)->getAlignment()->setWrapText(true);
    }

    /**
     *
     * @param string $cellName
     * @param string $borderType top, left, bottom, right, outline
     * @param string $color
     * @param string $lineStyle
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function setCellBorder($cellName, $borderType = 'outline', $color = null, $lineStyle = null)
    {
        if ($lineStyle == null) {
            $lineStyle = Border::BORDER_HAIR;
        }
        if ($color == null) {
            $color = '000000';
        }
        $borderStyle = array(
            'borders' => array(
                $borderType => array(
                    'style' => $lineStyle,
                    'color' => array('argb' => 'FF' . $color),
                ),
            ),
        );
        $this->getActiveSheet()->getStyle($cellName)->applyFromArray($borderStyle);
    }

    /**
     * @param Event       $event
     * @param string      $eventName
     * @param string|null $eventSuffix
     */
    protected function dispatchEvent(Event $event, $eventName, $eventSuffix = null)
    {
        if (null === $this->eventDispatcher || empty($this->excelOptions->getEventPrefix())) {
            return;
        }
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->eventDispatcher;

        $eventKey = $this->excelOptions->getEventPrefix() . '.export_excel.' . $eventName;
        if (!empty($eventSuffix)) {
            $eventKey .= '.' . $eventSuffix;
        }

        $dispatcher->dispatch($eventKey, $event);
    }

}
