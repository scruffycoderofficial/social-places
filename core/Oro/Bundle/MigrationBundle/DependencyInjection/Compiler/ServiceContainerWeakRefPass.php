<?php

/*
 * @codingStandardsIgnoreStart
 *
 * This file is a copy of {@see Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TestServiceContainerWeakRefPass}
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * @codingStandardsIgnoreEnd
 */

namespace Oro\Bundle\MigrationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Collects all private services and their aliases to build the service locator for the migration container.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ServiceContainerWeakRefPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('oro_migration.service_container')) {
            return;
        }

        $privateServices = [];
        $definitions = $container->getDefinitions();

        foreach ($definitions as $id => $definition) {
            if ($id && '.' !== $id[0]
                && (!$definition->isPublic() || $definition->isPrivate())
                && !$definition->getErrors()
                && !$definition->isAbstract()
                && $id !== 'oro_migration.service_container'
            ) {
                if (!$definition->isShared() && strpos($id, 'oro') === 0) {
                    $definition->setPublic(true);
                } else {
                    $privateServices[$id] = new ServiceClosureArgument(
                        new Reference($id, ContainerBuilder::IGNORE_ON_UNINITIALIZED_REFERENCE)
                    );
                }
            }
        }

        $aliases = $container->getAliases();

        foreach ($aliases as $id => $alias) {
            if ($id && '.' !== $id[0] && (!$alias->isPublic() || $alias->isPrivate())) {
                while (isset($aliases[$target = (string) $alias])) {
                    $alias = $aliases[$target];
                }
                if (isset($definitions[$target])
                    && !$definitions[$target]->getErrors()
                    && !$definitions[$target]->isAbstract()
                ) {
                    $privateServices[$id] = new ServiceClosureArgument(
                        new Reference($target, ContainerBuilder::IGNORE_ON_UNINITIALIZED_REFERENCE)
                    );
                }
            }
        }

        if ($privateServices) {
            $definition = $definitions[(string) $definitions['oro_migration.service_container']->getArgument(2)];
            $definition->replaceArgument(0, $privateServices);
        }
    }
}
