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
use SimpleThings\EntityAudit\Exception\NoRevisionFoundException;
use SimpleThings\EntityAudit\Exception\NotAuditedException;
use SimpleThings\EntityAudit\Revision;
use Sonata\AdminBundle\Model\AuditManager as SonataAuditManager;
use Sonata\AdminBundle\Model\AuditManagerInterface;
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
     * @var AuditManagerInterface|SonataAuditManager
     */
    protected $sonataAuditManager;
    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * @param AuditManagerInterface $sonataAuditManager
     * @param Environment $twigEnvironment
     */
    public function __construct(
        AuditManagerInterface $sonataAuditManager,
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
        $checkTstamp = time() - 10;
        // Check if there have actually been changes (i.e. a new revision hast been added)
        if ($revisionData['current_rev_timestamp'] >= $checkTstamp) {
            $content = $this->getContentForRevisions(
                $object,
                (int) $revisionData['previous_rev'],
                (int) $revisionData['current_rev'],
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
    public function getContentForRevisions(BaseEntityInterface $object, int $oldRevision, int $newRevision, string $renderType = self::RENDER_TYPE_HTML): string
    {
        $content = '';
        if ($id = $object->getId()) {
            try {
                $className = get_class($object);
                $auditReader = $this->sonataAuditManager->getReader($className);
                if (null === $auditReader) {
                    return '';
                }
                $baseObject = null;
                try {
                    if ($oldRevision > 0) {
                        $baseObject = $auditReader->find($className, $id, $oldRevision);
                    }
                } catch (NoRevisionFoundException $e) {
                    unset($e);
                }
                try {
                    $compareObject = $auditReader->find($className, $id, $newRevision);
                } catch (NoRevisionFoundException $e) {
                    $compareObject = null;
                }
                $admin = $this->adminManager->getAdminByEntityClass($className);
                if ((!$baseObject && !$compareObject) || !$admin) {
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
            'current_rev_timestamp' => null,
            'previous_rev_timestamp' => null,
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
                        /** @var Revision $revCurrent */
                        $revCurrent = $auditedRevisions[$keys[0]];
                        $revisions['current_rev'] = $revCurrent->getRev();
                        $revTstamp = $revCurrent->getTimestamp();
                        if ($revTstamp instanceof \DateTime) {
                            // Fix the date time; DateTime objects show timezone as Europe/Berlin, but are actually UTC
                            $fixDateTime = new \DateTime($revTstamp->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
                            $revTstamp = $fixDateTime->getTimestamp();
                        }
                        $revisions['current_rev_timestamp'] = $revTstamp;
                    }
                    if (count($auditedRevisions) > 1) {
                        /** @var Revision $revPrev */
                        $revPrev = $auditedRevisions[$keys[1]];
                        $revisions['previous_rev'] = $revPrev->getRev();
                        $revTstamp = $revPrev->getTimestamp();
                        if ($revTstamp instanceof \DateTime) {
                            // Fix the date time; DateTime objects show timezone as Europe/Berlin, but are actually UTC
                            $fixDateTime = new \DateTime($revTstamp->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
                            $revTstamp = $fixDateTime->getTimestamp();
                        }
                        $revisions['previous_rev_timestamp'] = $revTstamp;
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