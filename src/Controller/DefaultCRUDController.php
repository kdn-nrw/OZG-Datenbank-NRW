<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2023 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;


use App\Admin\AbstractContextAwareAdmin;
use App\Admin\CustomExportAdminInterface;
use Sonata\AdminBundle\Bridge\Exporter\AdminExporter;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\BadRequestParamHttpException;
use Sonata\Exporter\ExporterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class DefaultCRUDController
 */
class DefaultCRUDController extends CRUDController
{

    /**
     * Export data to specified format.
     *
     * @throws AccessDeniedException If access is not granted
     * @throws \RuntimeException     If the export format is invalid
     */
    public function exportAction(Request $request): Response
    {
        $this->admin->checkAccess('export');
        if ($this->admin instanceof CustomExportAdminInterface) {
            return $this->appAdminExport($request);
        }
        return parent::exportAction($request);
    }

    /**
     * Export data to specified format.
     * Use custom export settings for admins implementing CustomExportAdminInterface
     *
     * @throws AccessDeniedException If access is not granted
     * @throws \RuntimeException     If the export format is invalid
     */
    private function appAdminExport(Request $request): Response
    {
        /** @var CustomExportAdminInterface|AbstractContextAwareAdmin $admin */
        $admin = $this->admin;
        $admin->checkAccess('export');
        $format = $request->get('format');
        if (!\is_string($format)) {
            throw new BadRequestParamHttpException('format', 'string', $format);
        }

        $adminExporter = $this->container->get('sonata.admin.admin_exporter');
        \assert($adminExporter instanceof AdminExporter);
        $allowedExportFormats = $adminExporter->getAvailableFormats($admin);
        $filename = $adminExporter->getExportFilename($admin, $format);
        $exporter = $this->container->get('sonata.exporter.exporter');
        \assert($exporter instanceof ExporterInterface);

        if (!\in_array($format, $allowedExportFormats, true)) {
            throw new \RuntimeException(sprintf(
                'Export in format `%s` is not allowed for class: `%s`. Allowed formats are: `%s`',
                $format,
                $admin->getClass(),
                implode(', ', $allowedExportFormats)
            ));
        }

        return $exporter->getResponse(
            $format,
            $filename,
            $admin->getCustomDataSourceIterator()
        );
    }
}
