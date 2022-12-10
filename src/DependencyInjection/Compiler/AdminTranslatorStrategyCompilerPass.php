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

namespace App\DependencyInjection\Compiler;

use App\Admin\AbstractContextAwareAdmin;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Enable custom application translator strategy for all admins
 * @see \App\Translator\PrefixedUnderscoreLabelTranslatorStrategy
 */
class AdminTranslatorStrategyCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {

        foreach ($container->findTaggedServiceIds('sonata.admin') as $id => $attributes) {
            $definition = $container->getDefinition($id);
            $adminClass = $definition->getClass();
            if (is_a($adminClass, AbstractContextAwareAdmin::class, true)) {
                $definition->addMethodCall('configureAppTranslatorStrategy', [true]);
            }
        }
    }
}
