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

use App\Entity\Configuration\EmailTemplate;
use App\Entity\Onboarding\Release;
use App\Entity\Onboarding\XtaServer;
use Symfony\Component\Mime\Address;

class OnboardingXtaServerUpdateModel extends AbstractTemplateModel
{
    public const TEMPLATE_KEY = 'onboarding_xta_server_update';

    protected $entityClass = XtaServer::class;

    protected $templateLabel = 'app.email_template.entity.template_key_choices.xta_server_update';

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
     * @return Address|null
     */
    public function getRecipient(): ?Address
    {
        $entity = $this->variables['entity'] ?? null;
        if (null === $this->recipient && $entity instanceof XtaServer) {
            $type = $entity->getIntermediaryOperatorType();
            if ($type === XtaServer::INTERMEDIARY_OPERATOR_TYPE_2) {
                $this->recipient = new Address('dataclearing@citeq.de');
            }
            //default:
            //    $this->recipient = new Address('dataclearing@krzn.de');
        }
        return $this->recipient;
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
        $template->setDefaultRecipient('dataclearing@krzn.de');
        $template->setDescription('
Definition der E-Mail-Empfänger
Intermediär = „DataClearing NRW - KRZN“	=> Empfänger (Standard)
Intermediär = „DataClearing NRW - citeq“ => dataclearing@citeq.de
Intermediär = „DataClearing NRW - Zuordnung nicht bekannt“ => Empfänger (Standard)');
        $template->setSubject('Bauportal.NRW - XTA-Antrag  ###ONBOARDING_XTA_SERVER_COMMUNE### - Daten via KDN-Formular');
        $template->setBody('Die XTA Onboarding Information von  ###ONBOARDING_XTA_SERVER_COMMUNE### wurden von ###USER_FIRSTNAME### ###USER_LASTNAME### aktualisiert:
###ONBOARDING_XTA_SERVER_ADMIN_EDIT_URL###');
        return $template;
    }

}