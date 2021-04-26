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

use App\Entity\Configuration\EmailTemplate;
use App\Entity\Onboarding\Epayment;

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
###ONBOARDING_EPAYMENT_ADMIN_EDIT_URL###');
        return $template;
    }

}