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

namespace App\Exporter\Writer;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Sonata\Exporter\Writer\TypedWriterInterface;

class XlsxWriter implements TypedWriterInterface
{
    public const LABEL_COLUMN = 1;
    /** @var  Spreadsheet */
    private $phpExcelObject;
    /** @var array */
    private $headerColumns = [];
    /**
     * @var string
     */
    private $filename;

    /**
     * @var bool
     */
    private $showHeaders;

    /** @var int */
    protected $rowNumber;

    /**
     * @var int
     */
    private $currentColumn = 0;

    /**
     * XlsxWriter constructor.
     * @param string $filename
     * @param bool $showHeaders
     * @throws \RuntimeException
     */
    public function __construct(string $filename, bool $showHeaders = true)
    {
        $this->filename = $filename;
        $this->showHeaders = $showHeaders;
        $this->rowNumber = $showHeaders ? 2 : 1;

        if (is_file($filename)) {
            throw new \RuntimeException(sprintf('The file %s already exists', $filename));
        }
    }

    public function getDefaultMimeType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    public function getFormat(): string
    {
        return 'xlsx';
    }

    /**
     * Create PHPExcel object and set defaults
     */
    public function open()
    {
        $this->phpExcelObject = new Spreadsheet();
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $data)
    {
        $this->init($data);

        foreach ($data as $header => $value) {
            $this->setCellValue($this->getColumn($header), $value);
        }

        ++$this->rowNumber;
    }

    /**
     * Set labels
     * @param $data
     *
     * @return void
     */
    protected function init($data): void
    {
        if ($this->rowNumber > 2) {
            return;
        }
        $i = 0;
        foreach ($data as $header => $value) {
            $column = self::formatColumnName($i);
            $this->setHeader($column, $header);
            $i++;
        }

        $this->setBoldLabels();
    }

    /**
     * Save Excel file
     */
    public function close()
    {
        $writer = IOFactory::createWriter($this->phpExcelObject, 'Xlsx');
        $writer->save($this->filename);
    }

    /**
     * Returns letter for number based on Excel columns
     * @param int $number
     * @return string
     */
    public static function formatColumnName($number): string
    {
        for ($char = ''; $number >= 0; $number = (int)($number / 26) - 1) {
            $char = chr($number % 26 + 0x41) . $char;
        }
        return $char;
    }

    /**
     * @return Worksheet
     */
    private function getActiveSheet(): Worksheet
    {
        return $this->phpExcelObject->setActiveSheetIndex(0);
    }

    /**
     * Makes header bold
     */
    private function setBoldLabels(): void
    {
        $this->getActiveSheet()->getStyle(
            sprintf(
                "%s1:%s1",
                reset($this->headerColumns),
                end($this->headerColumns)
            )
        )->getFont()->setBold(true);
    }

    /**
     * Sets cell value
     * @param string $column
     * @param string $value
     */
    private function setCellValue($column, $value): void
    {
        $this->getActiveSheet()->setCellValue($column, $value);
    }

    /**
     * Set column label and make column auto size
     * @param string $column
     * @param string $value
     */
    private function setHeader($column, $value): void
    {
        if ($this->showHeaders) {
            $this->setCellValue($column . self::LABEL_COLUMN, $value);
            $this->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        }
        $this->headerColumns[$value] = $column;
    }

    /**
     * Get column name
     * @param string $name
     * @return string
     */
    private function getColumn($name): string
    {
        if (isset($this->headerColumns[$name])) {
            $this->currentColumn = $this->headerColumns[$name];
        } else {
            ++$this->currentColumn;
        }
        return $this->currentColumn . $this->rowNumber;
    }
}