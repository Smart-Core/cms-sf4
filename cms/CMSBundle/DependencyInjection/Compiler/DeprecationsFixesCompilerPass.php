<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeprecationsFixesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('annotations.reader')->setPublic(true);
        //$container->getDefinition('assetic.asset_manager')->setPublic(true);
        $container->getDefinition('assets.packages')->setPublic(true);
        $container->getDefinition('fos_user.user_manager')->setPublic(true);
        $container->getDefinition('liip_imagine.filter.configuration')->setPublic(true);
        $container->getDefinition('monolog.logger.request')->setPublic(true);
        $container->getDefinition('security.logout_url_generator')->setPublic(true);
        $container->getDefinition('templating.helper.assets')->setPublic(true);

        if ($container->hasDefinition('smart_media')) {
            $container->getDefinition('smart_media')->setPublic(true);
        }
    }
}
