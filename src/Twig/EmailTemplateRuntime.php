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

namespace App\Twig;

use App\Entity\Configuration\EmailTemplate;
use App\Model\EmailTemplate\AbstractTemplateModel;
use App\Service\Mailer\InjectEmailTemplateManagerTrait;
use Twig\Extension\RuntimeExtensionInterface;

/**
 */
final class EmailTemplateRuntime implements RuntimeExtensionInterface
{
    use InjectEmailTemplateManagerTrait;

    /**
     * @param string|EmailTemplate $keyOrEntity
     * @return AbstractTemplateModel|null
     */
    public function getEmailTemplateModel($keyOrEntity): ?AbstractTemplateModel
    {
        return $this->emailTemplateManager->getEmailTemplateModel($keyOrEntity);
    }

    /**
     * @param AbstractTemplateModel $model
     * @param bool $useTestData
     * @return array The model markers
     */
    public function getEmailTemplateMarkers(AbstractTemplateModel $model, bool $useTestData = false): array
    {
        return $this->emailTemplateManager->getModelMarkers($model, $useTestData);
    }
}
