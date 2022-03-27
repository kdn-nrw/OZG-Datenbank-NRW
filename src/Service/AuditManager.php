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

namespace App\Service;

use App\Entity\Base\BaseEntityInterface;
use SimpleThings\EntityAudit\Exception\NotAuditedException;
use Sonata\AdminBundle\Model\AuditManager as SonataAuditManager;
use Twig\Environment;

/**
 * Wrapper for audit reader
 */
class AuditManager
{
    public const RENDER_TYPE_TEXT = 'text';
    public const RENDER_TYPE_HTML = 'html';

    use InjectAdminManagerTrait;

    /**
     * @var SonataAuditManager
     */
    protected $sonataAuditManager;
    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * @param SonataAuditManager $sonataAuditManager
     * @param Environment $twigEnvironment
     */
    public function __construct(
        SonataAuditManager $sonataAuditManager,
        Environment        $twigEnvironment
    )
    {
        $this->sonataAuditManager = $sonataAuditManager;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function getChangesForEntity(BaseEntityInterface $object, string $renderType = self::RENDER_TYPE_HTML): string
    {
        $content = '';
        $revisionData = $this->getLatestRevisions($object);
        if ($revisionData['valid']) {
            $content = $this->getContentForRevisions(
                $object,
                $revisionData['previous_rev'],
                $revisionData['current_rev'],
                $renderType
            );
        }
        return $content;
    }

    /**
     * @param BaseEntityInterface $object
     * @param int $oldRevision
     * @param int $newRevision
     * @param string $renderType
     * @return string
     */
    public function getContentForRevisions(BaseEntityInterface $object, $oldRevision, $newRevision, string $renderType = self::RENDER_TYPE_HTML): string
    {
        $content = '';
        if ($id = $object->getId()) {
            try {
                $className = get_class($object);
                $auditReader = $this->sonataAuditManager->getReader($className);
                if (null === $auditReader) {
                    return '';
                }
                $baseObject = $auditReader->find($className, $id, $oldRevision);
                $compareObject = $auditReader->find($className, $id, $newRevision);
                $admin = $this->adminManager->getAdminByEntityClass($className);
                if (!$baseObject || !$compareObject || !$admin) {
                    return '';
                }
                $admin->setSubject($baseObject);
                $content = $this->twigEnvironment->render('EmailTemplate/Audit/base_show_compare.html.twig', [
                    'admin' => $admin,
                    'action' => 'show',
                    'object' => $baseObject,
                    'object_compare' => $compareObject,
                    'elements' => $admin->getShow(),
                ]);
                if ($renderType === self::RENDER_TYPE_TEXT) {
                    $content = \App\Util\TextConverter::htmlToPlainText($content);
                }
            } catch (NotAuditedException $e) {
                $content = '';
            }
        }
        return $content;
    }

    /**
     * Returns the current and previous revisions for the given object
     * @param BaseEntityInterface $object
     * @return array<string, mixed>
     */
    public function getLatestRevisions(BaseEntityInterface $object): array
    {
        $revisions = [
            'current_rev' => null,
            'previous_rev' => null,
            'valid' => false,
            'exception' => null,
        ];
        if ($id = $object->getId()) {
            try {
                $className = get_class($object);
                $auditReader = $this->sonataAuditManager->getReader($className);
                if (null !== $auditReader) {
                    $auditedRevisions = $auditReader->findRevisions($className, $id);
                    $keys = array_keys($auditedRevisions);
                    if (count($auditedRevisions) > 0) {
                        $revisions['current_rev'] = $auditedRevisions[$keys[0]]->getRev();
                    }
                    if (count($auditedRevisions) > 1) {
                        $revisions['previous_rev'] = $auditedRevisions[$keys[1]]->getRev();
                        $revisions['valid'] = true;
                    }
                }
            } catch (NotAuditedException $e) {
                $revisions['exception'] = $e;
            }
        }
        return $revisions;
    }
}