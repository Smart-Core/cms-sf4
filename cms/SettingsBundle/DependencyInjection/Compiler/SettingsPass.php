<?php

namespace SmartCore\Bundle\SettingsBundle\DependencyInjection\Compiler;

use Doctrine\ORM\Tools\SchemaValidator;
use SmartCore\Bundle\SettingsBundle\Manager\SettingsManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * @deprecated В sf4 приходит не с компилированный контейнер на TYPE_AFTER_REMOVING, по этому нету коннекта к бд.
 */
class SettingsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $lockFile = $container->getParameter('kernel.cache_dir').'/'.SettingsManager::LOCK_FILE;

        file_put_contents($lockFile, '');
    }
}
