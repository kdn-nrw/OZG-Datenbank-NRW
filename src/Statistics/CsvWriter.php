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

namespace App\Statistics;

use App\Statistics\Options\ExportOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV writer for statistics providers
 */
class CsvWriter
{

    /**
     * @var ExportHelper
     */
    private $exportHelper;

    /**
     * Constructor
     *
     * @param ExportHelper $exportHelper
     */
    public function __construct(ExportHelper $exportHelper)
    {
        $this->exportHelper = $exportHelper;
    }

    /**
     * The main function to do csv exports
     *
     * @param string $fileName
     * @param array $data
     * @param ExportOptions $options
     * @return Response
     */
    public function export($fileName, array $data, ExportOptions $options)
    {
        $headers = $this->exportHelper->generateHeaders($options);
        // $response = new Response($content);
        $response = new StreamedResponse();
        $response->setCallback(static function() use ($headers, $data) {
            $handle = fopen('php://output', 'wb+');
            // Add the header of the CSV file
            fputcsv($handle, $headers,';');
            // Add the data queried from database
            foreach ($data as $row) {
                fputcsv(
                    $handle, // The file pointer
                    $row, // The fields
                    ';' // The delimiter
                );
            }
            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');

        return $response;
    }
}