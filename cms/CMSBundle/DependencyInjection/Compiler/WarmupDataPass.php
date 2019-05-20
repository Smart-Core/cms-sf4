<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\DependencyInjection\Compiler;

use Monolith\CMSBundle\CMSBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WarmupDataPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $lockFile = $container->getParameter('kernel.cache_dir').'/'.CMSBundle::LOCK_FILE;

        file_put_contents($lockFile, '');
    }
}
