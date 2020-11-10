<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DependencyInjection\Compiler;

use App\Api\Consumer\ApiManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ApiManagerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has(ApiManager::class)) {
            return;
        }

        $definition = $container->findDefinition(ApiManager::class);

        // or processing tagged services:
        foreach ($container->findTaggedServiceIds('app.api_consumer') as $id => $tags) {
            $definition->addMethodCall('addConsumer', array(new Reference($id)));
        }
    }
}