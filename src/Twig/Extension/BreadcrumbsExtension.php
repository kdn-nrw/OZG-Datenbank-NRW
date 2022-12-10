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

namespace App\Twig\Extension;

use App\Twig\BreadcrumbsRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Use extended BreadcrumbsRuntime for application
 * @see \Sonata\AdminBundle\Twig\Extension\BreadcrumbsExtension
 */
final class BreadcrumbsExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_render_breadcrumbs', [BreadcrumbsRuntime::class, 'renderBreadcrumbs'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
            new TwigFunction('app_render_breadcrumbs_for_title', [BreadcrumbsRuntime::class, 'renderBreadcrumbsForTitle'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }
}
