<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig\Extension;

use App\Entity\Configuration\EmailTemplate;
use App\Model\EmailTemplate\AbstractTemplateModel;
use App\Service\Mailer\InjectEmailTemplateManagerTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EmailTemplateExtension extends AbstractExtension
{
    use InjectEmailTemplateManagerTrait;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_email_template_get_model', [$this, 'getEmailTemplateModel']),
            new TwigFunction('app_email_template_get_markers', [$this, 'getEmailTemplateMarkers']),
        ];
    }

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
    public function getEmailTemplateMarkers(AbstractTemplateModel $model, $useTestData = false): array
    {
        return $this->emailTemplateManager->getModelMarkers($model, $useTestData);
    }
}
