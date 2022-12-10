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

use App\Twig\EmailTemplateRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EmailTemplateExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_email_template_get_model', [EmailTemplateRuntime::class, 'getEmailTemplateModel'], [
                'is_safe' => ['html'],
                'needs_environment' => false,
            ]),
            new TwigFunction('app_email_template_get_markers', [EmailTemplateRuntime::class, 'getEmailTemplateMarkers'], [
                'is_safe' => ['html'],
                'needs_environment' => false,
            ]),
        ];
    }
}
