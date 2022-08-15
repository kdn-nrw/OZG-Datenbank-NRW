<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Onboarding;


use App\Controller\ControllerDownloadTrait;

/**
 * Class MonumentAuthorityAdminController
 */
class MonumentAuthorityAdminController extends AbstractOnboardingAdminController
{
    use ControllerDownloadTrait;

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        parent::configure();
        $templateRegistry = $this->admin->getTemplateRegistry();
        if (null !== $templateRegistry) {
            $templateRegistry->setTemplate('edit', 'Onboarding/MonumentAuthority/edit.html.twig');
        }
    }
}
