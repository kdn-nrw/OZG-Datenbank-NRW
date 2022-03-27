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

use App\Admin\Base\AuditedEntityAdminInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Enable history action for audited entities in sonata admin
 */
class AddAuditEntityCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('simplethings_entityaudit.config')) {
            return;
        }

        $auditedEntities = $container->getParameter('simplethings.entityaudit.audited_entities');


        foreach ($container->findTaggedServiceIds('sonata.admin') as $id => $attributes) {
            if ('orm' !== $attributes[0]['manager_type']) {
                continue;
            }

            $definition = $container->getDefinition($id);
            $adminClass = $definition->getClass();
            if ((null !== $modelOrParameterName = $definition->getArgument(1))
                && is_a($adminClass, AuditedEntityAdminInterface::class, true)) {
                $modelName = $this->getModelName($container, $modelOrParameterName);
                if (in_array($modelName, $auditedEntities, false)) {
                    $definition->addMethodCall('setEntityAuditEnabled', [true]);
                }
            }
        }
    }

    private function getModelName(ContainerBuilder $container, string $name): string
    {
        if ('%' === $name[0]) {
            return $container->getParameter(substr($name, 1, -1));
        }

        return $name;
    }
}
