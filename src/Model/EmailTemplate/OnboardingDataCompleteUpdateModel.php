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
use App\Entity\Onboarding\AbstractOnboardingEntity;

class OnboardingDataCompleteUpdateModel extends AbstractTemplateModel
{
    public const TEMPLATE_KEY = 'onboarding_data_complete_update';

    protected $entityClass = AbstractOnboardingEntity::class;

    protected $templateLabel = 'app.email_template.entity.template_key_choices.onboarding_data_complete_update';

    /**
     * @var string|null
     */
    protected $templateKey = self::TEMPLATE_KEY;

    /**
     * Custom marker group name; only needs to be filled, if different from the local entity class name
     * @var string|null
     */
    protected $markerGroup = 'ONBOARDING_RECORD';

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
        $template->setDefaultRecipient('kommunalportal@kdn.de');
        $template->setSubject('Onboarding ###ONBOARDING_RECORD_CLASS_LABEL###: Kommune ###ONBOARDING_RECORD_COMMUNE###: Alle notwendigen Felder ausgefüllt');
        $template->setBody('Die Kommune ###ONBOARDING_RECORD_COMMUNE### hat bestätigt, alle notwendigen Felder in Onboarding ###ONBOARDING_RECORD_CLASS_LABEL### ausgefüllt zu haben.
Bestätigung durch: ###USER_FIRSTNAME### ###USER_LASTNAME###
###ONBOARDING_RECORD_ADMIN_EDIT_URL###');
        return $template;
    }

    /**
     * @inheritDoc
     */
    public function isMatch(BaseEntityInterface $object, string $processType): bool
    {
        return $processType === self::PROCESS_TYPE_UPDATE
            && $object instanceof AbstractOnboardingEntity
            && $object->isDataCompletenessConfirmed()
            && $object->getCompletionRate() === 100
            && $object->getStatus() < AbstractOnboardingEntity::STATUS_COMPLETE_CONFIRMED;
    }

}