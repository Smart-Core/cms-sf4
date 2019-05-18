<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Подмена в маршруте _folderPath для модулей.
 */
class ChangeRouterClassPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $router = $container->getDefinition('router.default');

        $router->setClass($container->getParameter('router.class'));
    }
}
