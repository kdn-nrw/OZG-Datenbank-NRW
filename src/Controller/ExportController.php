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

namespace App\Controller;


use App\Admin\AbstractContextAwareAdmin;
use App\Exporter\Source\CustomResultSetSourceIterator;
use App\Service\ApplicationContextHandler;
use App\Service\InjectAdminManagerTrait;
use Doctrine\Common\Collections\Collection;
use Sonata\AdminBundle\Bridge\Exporter\AdminExporter;
use Sonata\Exporter\Exporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ExportController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 * @since     2021-02-28
 */
class ExportController extends AbstractController
{
    use InjectAdminManagerTrait;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var AdminExporter
     */
    private $adminExporter;
    /**
     * @var Exporter
     */
    private $exporter;

    /**
     * SearchController constructor.
     * @param SessionInterface $session
     * @param Exporter $exporter
     * @param AdminExporter $adminexporter
     * @param TranslatorInterface $translator
     */
    public function __construct(
        SessionInterface $session,
        Exporter $exporter,
        AdminExporter $adminexporter,
        TranslatorInterface $translator
    )
    {
        $this->translator = $translator;
        $this->session = $session;
        $this->adminExporter = $adminexporter;
        $this->exporter = $exporter;
    }

    /**
     * Export data to specified format.
     *
     * @param Request $request
     * @param string $recordType
     * @param int $recordId
     * @param string $property
     * @return Response
     */
    public function adminExportAction(Request $request, string $recordType, int $recordId, string $property): Response
    {
        return $this->exportAction($request, $recordType, $recordId, $property);
    }

    /**
     * Export data to specified format.
     *
     * @param Request $request
     * @param string $recordType
     * @param int $recordId
     * @param string $property
     * @return Response
     */
    public function exportAction(Request $request, string $recordType, int $recordId, string $property): Response
    {
        $format = $request->get('format') ?? 'xlsx';
        $admin = $this->adminManager->getAdminInstance($recordType);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if ($admin->hasAccess('export')
            && (null !== $object = $admin->getModelManager()->find($admin->getClass(), $recordId))
            && $propertyAccessor->isReadable($object, $property)) {
            $admin->setSubject($object);
            $show = $admin->getShow();
            if (null === $show || !$show->has($property)) {
                throw new \InvalidArgumentException('The property %s is invalid for the admin %s', $property, $admin->getCode());
            }
            $fieldDescription = $show->get($property);
            $refSettings = $this->adminManager->getConfigurationForEntityProperty($object, $fieldDescription->getName());
            $exportAdmin = $fieldDescription->getAssociationAdmin();
            if (null === $exportAdmin && $adminCode = $fieldDescription->getOption('admin_code')) {
                $exportAdmin = $this->adminManager->getAdminInstance($adminCode);
            }
            if (null === $exportAdmin || !($exportAdmin instanceof AbstractContextAwareAdmin)) {
                throw new \InvalidArgumentException('The association admin is null for the property %s in the admin %s', $property, $admin->getCode());
            }
            $adminExporter = $this->adminExporter;
            $allowedExportFormats = $adminExporter->getAvailableFormats($admin);

            if (!\in_array($format, $allowedExportFormats, true)) {
                throw new \InvalidArgumentException(sprintf(
                    'Export in format `%s` is not allowed for class: `%s`. Allowed formats are: `%s`',
                    $format,
                    $exportAdmin->getClass(),
                    implode(', ', $allowedExportFormats)
                ));
            }
            $exportValues = $propertyAccessor->getValue($object, $property);
            if (!($exportValues instanceof Collection)) {
                throw new \RuntimeException(sprintf(
                    'Only collections can be exported. The property `%s` has an incorrect data type.',
                    $property
                ));
            }
            $filename = sprintf(
                'export_%s_%s.%s',
                str_replace([' ', '.', ';', ':'], '_', strtolower(
                    $object . '_' . $this->translator->trans($refSettings['default_label'])
                )),
                date('Ymd_His', time()),
                $format
            );
            $exportSettings = $exportAdmin->getProcessedExportSettings();
            ini_set('max_execution_time', 0);
            $exportSettings->setContext(ApplicationContextHandler::getDefaultAdminApplicationContext($exportAdmin));
            $dataSourceIterator = new CustomResultSetSourceIterator(
                $exportValues,
                $this->adminManager->getCache(),
                $exportSettings
            );
            return $this->exporter->getResponse($format, $filename, $dataSourceIterator);
        }
        throw new AccessDeniedException(sprintf('Access Denied to the export action for type %s and property %s', $recordType, $property));
    }
}
