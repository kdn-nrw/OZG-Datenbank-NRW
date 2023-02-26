<?php
declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\EmailTemplate;

use App\Entity\Base\BaseEntityInterface;
use App\Entity\Configuration\EmailTemplate;
use App\Entity\Onboarding\PmPayment;
use App\Service\AuditManager;

class OnboardingPmPaymentUpdateModel extends AbstractTemplateModel
{
    public const TEMPLATE_KEY = 'onboarding_pm_payment_update';

    protected $entityClass = PmPayment::class;

    protected $templateLabel = 'app.email_template.entity.template_key_choices.pm_payment_update';

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
        $template->setSubject('Änderung Onboarding pmPayment ###ONBOARDING_PM_PAYMENT_COMMUNE###');
        $template->setBody('Die pmPayment-Daten von ###ONBOARDING_PM_PAYMENT_COMMUNE### wurden von ###USER_FIRSTNAME### ###USER_LASTNAME### aktualisiert:
###ONBOARDING_PM_PAYMENT_ADMIN_EDIT_URL###
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
        if ($object instanceof PmPayment) {
            $revisionData = $auditManager->getLatestRevisions($object);
            $checkTstamp = time() - 10;
            $hasChangesMain = $revisionData['current_rev_timestamp'] >= $checkTstamp;
            $hasChanges = $hasChangesMain;
            $revisionChangedEntityMap = [];
            $this->addCollectionRevisionMeta($revisionChangedEntityMap, $object->getPmPaymentServices(), $auditManager, $checkTstamp);
            if (!empty($revisionChangedEntityMap)) {
                $hasChanges = true;
            }
            // Check if there have actually been changes (i.e. a new revision hast been added)
            if ($hasChanges) {
                if ($hasChangesMain) {
                    $changesContent .= $auditManager->getContentForRevisions(
                        $object,
                        (int)$revisionData['previous_rev'],
                        (int)$revisionData['current_rev'],
                        AuditManager::RENDER_TYPE_TEXT
                    );
                }
                $changesContent .= $this->getChangeRevisionEntitiesContent($auditManager, $revisionChangedEntityMap);
            }
        }
        return trim($changesContent);
    }

}