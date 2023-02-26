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

namespace App\DependencyInjection\Compiler;

use App\Admin\CustomFieldAdminInterface;
use App\Admin\Extension\CustomFieldExtension;
use App\Admin\Extension\OnboardingExtension;
use App\Admin\Onboarding\AbstractOnboardingAdmin;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AdminConfigurationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $adminExtensionServiceDefinition = $container->getDefinition(CustomFieldExtension::class);
        $onboardingExtensionServiceDefinition = $container->getDefinition(OnboardingExtension::class);
        foreach ($container->findTaggedServiceIds('sonata.admin') as $id => $tags) {
            $adminServiceDefinition = $container->getDefinition($id);
            $class = $adminServiceDefinition->getClass();
            if (is_subclass_of($class, CustomFieldAdminInterface::class, true)) {
                $adminExtensionServiceDefinition->addTag('sonata.admin.extension', ['target' => $id]);
                // This should normally be executed in
                // \Sonata\AdminBundle\DependencyInjection\Compiler\ExtensionCompilerPass::addExtension
                // but that does not work
                $adminServiceDefinition->addMethodCall('addExtension', [new Reference(CustomFieldExtension::class)]);
            }
            if (is_subclass_of($class, AbstractOnboardingAdmin::class, true)) {
                $onboardingExtensionServiceDefinition->addTag('sonata.admin.extension', ['target' => $id]);
                // This should normally be executed in
                // \Sonata\AdminBundle\DependencyInjection\Compiler\ExtensionCompilerPass::addExtension
                // but that does not work
                $adminServiceDefinition->addMethodCall('addExtension', [new Reference(OnboardingExtension::class)]);
            }
        }
    }
}