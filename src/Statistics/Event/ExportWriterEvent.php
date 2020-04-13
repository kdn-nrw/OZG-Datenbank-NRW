<?php

namespace App\Statistics\Event;

use App\Statistics\ExcelWriter;
use Symfony\Contracts\EventDispatcher\Event;


/**
 * Class PhpExcelEvent
 */
class ExportWriterEvent extends Event
{
    /**
     * @var ExcelWriter
     */
    private $excelWriter;

    /**
     * ExcelWriterEvent constructor.
     * @param ExcelWriter $excelWriter
     */
    public function __construct(ExcelWriter $excelWriter) {
        $this->excelWriter = $excelWriter;
    }

    /**
     * @return ExcelWriter
     */
    public function getExcelWriter() {
        return $this->excelWriter;
    }
}