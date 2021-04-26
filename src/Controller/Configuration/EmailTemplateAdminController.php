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

namespace App\Controller\Configuration;

use App\Service\Mailer\InjectEmailTemplateManagerTrait;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EmailTemplateAdminController
 *
 */
class EmailTemplateAdminController extends CRUDController
{
    use InjectEmailTemplateManagerTrait;

    /**
     * @inheritDoc
     */
    protected function preList(Request $request)
    {
        $this->emailTemplateManager->createTemplates();
        return null;
    }
}
