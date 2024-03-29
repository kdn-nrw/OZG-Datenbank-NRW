<?php
declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\EmailTemplate;

use App\Entity\Base\BaseEntityInterface;
use App\Entity\Configuration\EmailTemplate;
use App\Service\AuditManager;
use App\Util\SnakeCaseConverter;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractTemplateModel
{
    public const PROCESS_TYPE_CREATE = 'create';
    public const PROCESS_TYPE_UPDATE = 'update';


    public const VARIABLE_KEY_AUDIT = 'changes';

    /**
     * @var string|null
     */
    protected $templateKey;

    /**
     * @var string|null
     */
    protected $templateLabel;

    /**
     * @var EmailTemplate
     */
    protected $emailTemplate;

    /**
     * @var UserInterface|null
     */
    protected $user;

    /**
     * @var array|EmailVariableModel[]
     */
    protected $variableDefinitions;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var Address|null
     */
    protected $recipient;

    /**
     * Custom marker group name; only needs to be filled, if different from the local entity class name
     * @var string|null
     */
    protected $markerGroup;

    /**
     * @return string|null
     */
    public function getTemplateKey(): ?string
    {
        return $this->templateKey;
    }

    /**
     * Returns the variables expected by this model
     * @return array|EmailVariableModel[]
     */
    public function getVariableDefinitions(): array
    {
        if (null === $this->variableDefinitions) {
            $this->initializeVariableDefinitions();
        }
        return $this->variableDefinitions;
    }

    /**
     * Initializes the variables definitions for this model
     */
    abstract public function initializeVariableDefinitions(): void;

    /**
     * @return EmailTemplate
     */
    public function getEmailTemplate(): EmailTemplate
    {
        return $this->emailTemplate;
    }

    /**
     * @param EmailTemplate $emailTemplate
     */
    public function setEmailTemplate(EmailTemplate $emailTemplate): void
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     */
    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setVariable(string $key, $value): void
    {
        $this->variables[$key] = $value;
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    /**
     * @return string|null
     */
    public function getTemplateLabel(): ?string
    {
        return $this->templateLabel;
    }

    /**
     * Returns a new EmailTemplate with default values for the current model
     * @return EmailTemplate
     */
    public function newEmailTemplateInstance(): EmailTemplate
    {
        $template = new EmailTemplate($this->getTemplateKey());
        $template->setHidden(true);
        return $template;
    }

    /**
     * @return string|null
     */
    public function getMarkerGroup(): ?string
    {
        return $this->markerGroup;
    }

    /**
     * @return Address|null
     */
    public function getRecipient(): ?Address
    {
        return $this->recipient;
    }

    /**
     * @param Address|null $recipient
     */
    public function setRecipient(?Address $recipient): void
    {
        $this->recipient = $recipient;
    }

    /**
     * Returns true, if the email template has been initialized
     */
    public function isInitialized(): bool
    {
        return null !== $this->emailTemplate;
    }

    /**
     * Returns true, if the email template is enabled for the given object and process type
     * @param BaseEntityInterface $object
     * @param string $processType
     * @return bool
     */
    public function isMatch(BaseEntityInterface $object, string $processType): bool
    {
        $key = self::getEntityTemplateKey(get_class($object), $processType);
        return $key === $this->templateKey;
    }

    /**
     * Check if all data for the email are set; this is called after all variables are set
     *
     * @param BaseEntityInterface $object
     * @param string $processType
     * @return bool
     */
    public function isValid(BaseEntityInterface $object, string $processType): bool
    {
        if ($processType === self::PROCESS_TYPE_UPDATE) {
            return !empty($this->variables[self::VARIABLE_KEY_AUDIT]);
        }
        return true;
    }

    /**
     * Returns the internal key for the given entity class
     *
     * @param string $entityClass
     * @param string $processType
     * @return string
     */
    private static function getEntityTemplateKey(string $entityClass, string $processType = ''): string
    {
        $entityKey = trim(str_replace(['\\', 'App_Entity'], ['_', ''], $entityClass), ' _');
        $entityKey = strtoupper(SnakeCaseConverter::camelCaseToSnakeCase($entityKey));
        return strtolower($entityKey . ($processType ? '_' . $processType : ''));
    }

    /**
     * Optionally add the audit content; this is needed here, because the relevant entities depend on the main
     * entity, the specific email template belongs to;
     * @see getObjectAuditContent for extending the audit content
     *
     * @param AuditManager $auditManager
     * @param BaseEntityInterface $object
     * @return void
     */
    public function addAuditContent(AuditManager $auditManager, BaseEntityInterface $object): void
    {
        $this->setVariable(self::VARIABLE_KEY_AUDIT, $this->getObjectAuditContent($auditManager, $object));
    }

    /**
     * Optionally add the audit content; this is needed here, because the relevant entities depend on the main
     * entity, the specific email template belongs to;
     * The default function only adds the changes for the given entity
     *
     * @param AuditManager $auditManager
     * @param BaseEntityInterface $object
     * @return string
     */
    protected function getObjectAuditContent(AuditManager $auditManager, BaseEntityInterface $object): string
    {
        return $auditManager->getChangesForEntity(
            $object,
            AuditManager::RENDER_TYPE_TEXT
        );
    }

    /**
     * Add change information for the given collection
     *
     * @param array $revisionChangedEntityMap
     * @param Collection $collection
     * @param AuditManager $auditManager
     * @param int $checkTstamp
     * @return bool
     */
    final protected function addCollectionRevisionMeta(array &$revisionChangedEntityMap, Collection $collection, AuditManager $auditManager, int $checkTstamp): bool
    {
        $collectionHasChanges = false;
        foreach ($collection as $entity) {
            if ($entity instanceof BaseEntityInterface) {
                if ($this->addEntityRevisionMeta($revisionChangedEntityMap, $entity, $auditManager, $checkTstamp)) {
                    $collectionHasChanges = true;
                }
            }
        }
        return $collectionHasChanges;
    }

    /**
     * Add change information for the given entity
     *
     * @param array $revisionChangedEntityMap
     * @param BaseEntityInterface $entity
     * @param AuditManager $auditManager
     * @param int $checkTstamp
     * @return bool
     */
    final protected function addEntityRevisionMeta(array &$revisionChangedEntityMap, BaseEntityInterface $entity, AuditManager $auditManager, int $checkTstamp): bool
    {
        $revisionData = $auditManager->getLatestRevisions($entity);
        $hasRevChanges = $revisionData['current_rev_timestamp'] >= $checkTstamp;
        if ($hasRevChanges) {
            $revisionChangedEntityMap[] = [
                'entity' => $entity,
                'revision_data' => $revisionData,
            ];
        }
        return $hasRevChanges;
    }

    /**
     * Returns the email content with changes for the given entities
     *
     * @param AuditManager $auditManager
     * @param array $revisionChangedEntityMap
     * @return string
     */
    final protected function getChangeRevisionEntitiesContent(AuditManager $auditManager, array $revisionChangedEntityMap): string
    {
        $changesContent = '';
        if (!empty($revisionChangedEntityMap)) {
            foreach ($revisionChangedEntityMap as $revEntry) {
                if (!empty($revEntry['revision_data'])) {
                    $revisionData = $revEntry['revision_data'];
                    $revChangesContent = $auditManager->getContentForRevisions(
                        $revEntry['entity'],
                        (int)$revisionData['previous_rev'],
                        (int)$revisionData['current_rev'],
                        AuditManager::RENDER_TYPE_TEXT
                    );
                    $changesContent .= PHP_EOL . PHP_EOL . $revChangesContent;
                }
            }
        }
        return $changesContent;
    }

}