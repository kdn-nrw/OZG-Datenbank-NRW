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
use App\Entity\Onboarding\CommuneInfo;
use App\Service\AuditManager;

class OnboardingCommuneInfoUpdateModel extends AbstractTemplateModel
{
    public const TEMPLATE_KEY = 'onboarding_commune_info_update';

    protected $entityClass = CommuneInfo::class;

    protected $templateLabel = 'app.email_template.entity.template_key_choices.commune_info_update';

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
        $template->setSubject('Änderung Onboarding Basisdaten ###ONBOARDING_COMMUNE_INFO_COMMUNE###');
        $template->setBody('Die Onboarding Basisdaten von ###ONBOARDING_COMMUNE_INFO_COMMUNE### wurden von ###USER_FIRSTNAME### ###USER_LASTNAME### aktualisiert:
###ONBOARDING_COMMUNE_INFO_ADMIN_EDIT_URL###
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
        $changesContent = parent::getObjectAuditContent($auditManager, $object);
        if ($object instanceof CommuneInfo) {
            $checkTstamp = time() - 10;
            $revisionChangedEntityMap = [];
            $this->addCollectionRevisionMeta($revisionChangedEntityMap, $object->getContacts(), $auditManager, $checkTstamp);
            $this->addCollectionRevisionMeta($revisionChangedEntityMap, $object->getDocuments(), $auditManager, $checkTstamp);
            //$this->addCollectionRevisionMeta($revisionChangedEntityMap, $object->getCommuneSolutions(), $auditManager, $checkTstamp);
            $changesContent .= $this->getChangeRevisionEntitiesContent($auditManager, $revisionChangedEntityMap);
        }
        return trim($changesContent);
    }

}