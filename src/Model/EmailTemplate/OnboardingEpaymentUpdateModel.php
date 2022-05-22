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
use App\Entity\Onboarding\Epayment;
use App\Service\AuditManager;

class OnboardingEpaymentUpdateModel extends AbstractTemplateModel
{
    public const TEMPLATE_KEY = 'onboarding_epayment_update';

    protected $entityClass = Epayment::class;

    protected $templateLabel = 'app.email_template.entity.template_key_choices.epayment_update';

    /**
     * @var string|null
     */
    protected $templateKey = self::TEMPLATE_KEY;

    /**
     * Initializes the variables expected by this model
     */
    public function initializeVariableDefinitions(): void
    {
        $this->variableDefinitions = [];
        $this->variableDefinitions[] = new EmailVariableModel('entity', 'entity', [
            'class' => $this->entityClass,
        ]);
    }

    /**
     * Returns a new EmailTemplate with default values for the current model
     * @return EmailTemplate
     */
    public function newEmailTemplateInstance(): EmailTemplate
    {
        $template = parent::newEmailTemplateInstance();
        $template->setSenderEmail('service@kdn.de');
        $template->setSenderName('KDN OZG');
        $template->setSubject('Änderung Onboarding ePayBL ###ONBOARDING_EPAYMENT_COMMUNE###');
        $template->setBody('Die ePayBL-Daten von ###ONBOARDING_EPAYMENT_COMMUNE### wurden von ###USER_FIRSTNAME### ###USER_LASTNAME### aktualisiert:
###ONBOARDING_EPAYMENT_ADMIN_EDIT_URL###
###CHANGES###');
        return $template;
    }

    /**
     * Extend audit content for related entities
     * @param AuditManager $auditManager
     * @param BaseEntityInterface $object
     * @return string
     */
    protected function getObjectAuditContent(AuditManager $auditManager, BaseEntityInterface $object): string
    {
        $changesContent = '';
        if ($object instanceof Epayment) {
            $contact = $object->getPaymentUser();
            $revisionData = $auditManager->getLatestRevisions($object);
            $checkTstamp = time() - 10;
            $hasChangesMain = $revisionData['current_rev_timestamp'] >= $checkTstamp;
            $hasChanges = $revisionData['current_rev_timestamp'] >= $checkTstamp;
            if (null !== $contact) {
                $contactRevisionData = $auditManager->getLatestRevisions($contact);
                $hasChanges = $hasChanges || $contactRevisionData['current_rev_timestamp'] >= $checkTstamp;
            }
            // Check if there have actually been changes (i.e. a new revision hast been added)
            if ($hasChanges) {
                if ($hasChangesMain) {
                    $changesContent .= $auditManager->getContentForRevisions(
                        $object,
                        (int) $revisionData['previous_rev'],
                        $revisionData['current_rev'],
                        AuditManager::RENDER_TYPE_TEXT
                    );
                }
                if (null !== $contact && !empty($contactRevisionData)) {
                    $contactChangesContent = $auditManager->getContentForRevisions(
                        $contact,
                        (int) $contactRevisionData['previous_rev'],
                        (int) $contactRevisionData['current_rev'],
                        AuditManager::RENDER_TYPE_TEXT
                    );
                    $changesContent .= PHP_EOL . PHP_EOL . $contactChangesContent;
                }
            }
        }
        return trim($changesContent);
    }

}