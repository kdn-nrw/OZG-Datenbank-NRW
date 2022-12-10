<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\ModelRegion;

use App\Entity\ModelRegion\ModelRegionProject;
use App\Exporter\Pdf\ConceptPdfExporter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Throwable;

/**
 * Trait ControllerProjectPdfExportTrait
 * @method createNotFoundException(string $message = 'Not Found', Throwable $previous = null): NotFoundHttpException
 */
trait ControllerProjectPdfExportTrait
{

    /**
     * @var ConceptPdfExporter
     */
    private $conceptPdfExporter;

    /**
     * @required
     * @param ConceptPdfExporter $conceptPdfExporter
     */
    public function injectConceptPdfExporter(ConceptPdfExporter $conceptPdfExporter): void
    {
        $this->conceptPdfExporter = $conceptPdfExporter;
    }

    /**
     * Get the applicant attachment as binary response
     *
     * @param Request $request
     * @param int|string|null $id
     * @return Response
     */
    public function exportPdfConceptAction(Request $request, $id = null): Response
    {
        // This is strange, but used like this in \Sonata\AdminBundle\Controller\CRUDController::showAction too
        /** @noinspection SuspiciousAssignmentsInspection */
        $id = (int)$request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!($object instanceof ModelRegionProject)) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id or object has incorrect type: %s', $id));
        }

        $this->admin->checkAccess('show', $object);

        $exportFileInfo = $this->conceptPdfExporter->exportForProject($object);
        return $this->file($exportFileInfo['abs_path'], $exportFileInfo['filename'], ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }
}