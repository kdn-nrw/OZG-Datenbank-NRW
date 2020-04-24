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