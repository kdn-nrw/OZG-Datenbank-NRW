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

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\BreadcrumbsBuilderInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Sonata admin bundle only allows a single template for the breadcrumb; we need separate templates for frontend and
 * backend
 * @see \Sonata\AdminBundle\Twig\BreadcrumbsRuntime
 */
final class BreadcrumbsRuntime implements RuntimeExtensionInterface
{
    private BreadcrumbsBuilderInterface $breadcrumbsBuilder;

    /**
     * @internal This class should only be used through Twig
     */
    public function __construct(BreadcrumbsBuilderInterface $breadcrumbsBuilder)
    {
        $this->breadcrumbsBuilder = $breadcrumbsBuilder;
    }

    /**
     * @param Environment $environment
     * @param AdminInterface<object> $admin
     * @param string $action
     * @param string $template
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @phpstan-template T of object
     */
    public function renderBreadcrumbs(
        Environment $environment,
        AdminInterface $admin,
        string $action,
        string $template = '@SonataAdmin/Breadcrumb/breadcrumb.html.twig'
    ): string {
        return $environment->render($template, [
            'items' => $this->breadcrumbsBuilder->getBreadcrumbs($admin, $action),
        ]);
    }

    /**
     * @param AdminInterface<object> $admin
     *
     * @phpstan-template T of object
     * @phpstan-param AdminInterface<T> $admin
     */
    public function renderBreadcrumbsForTitle(
        Environment $environment,
        AdminInterface $admin,
        string $action,
        string $template = '@SonataAdmin/Breadcrumb/breadcrumb_title.html.twig'
    ): string {
        return $environment->render($template, [
            'items' => $this->breadcrumbsBuilder->getBreadcrumbs($admin, $action),
        ]);
    }
}
