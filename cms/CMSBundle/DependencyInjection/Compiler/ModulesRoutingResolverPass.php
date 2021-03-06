<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\DependencyInjection\Compiler;

use Monolith\CMSBundle\Manager\ModuleManager;
use Monolith\CMSBundle\Module\ModuleBundleInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Yaml;

/**
 * Обход всех модулей и создание сервисов роутингов для каждого.
 */
class ModulesRoutingResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getParameter('kernel.bundles') as $moduleName => $bundle) {
            if (!in_array(ModuleBundleInterface::class, (new \ReflectionClass($bundle))->getInterfaceNames())) {
                continue;
            }

            foreach (ModuleManager::getNodeControllers(new $bundle) as $controller) {
                $redlectionClass = new \ReflectionClass($controller['class']);

                $definition = new Definition(
                    'Symfony\\Component\\Routing\\Router', [
                        new Reference('routing.loader.annotation.file'),
                        $redlectionClass->getFileName(), [
                            'cache_dir' => $container->getParameter('kernel.cache_dir').'/monolith_cms',
                            'debug'     => $container->getParameter('kernel.debug'),
                            'matcher_cache_class'   => 'CMSModule'.$moduleName.'UrlMatcher',
                            'generator_cache_class' => 'CMSModule'.$moduleName.'UrlGenerator',
                        ],
                    ]
                );
                $definition->addTag('cms_router_module');
                $definition->setPublic(true);

                $container->setDefinition('cms.router_module.'.$controller['class'], $definition);
            }

            foreach (ModuleManager::getAdminControllers(new $bundle) as $controller) {
                $redlectionClass = new \ReflectionClass($controller['class']);

                $definition = new Definition(
                    'Symfony\\Component\\Routing\\Router', [
                        new Reference('routing.loader.annotation.file'),
                        $redlectionClass->getFileName(), [
                            'cache_dir' => $container->getParameter('kernel.cache_dir').'/monolith_cms',
                            'debug'     => $container->getParameter('kernel.debug'),
                            'matcher_cache_class'   => 'CMSModule'.$moduleName.'UrlMatcher',
                            'generator_cache_class' => 'CMSModule'.$moduleName.'UrlGenerator',
                        ],
                    ]
                );
                $definition->addTag('cms_router_module_admin');
                $definition->setPublic(true);

                // Сохранение списка сервисов маршрутов, чтобы можно было быстро перебрать их на название роутов.
                $cms_router_module_admin = $container->hasParameter('cms_router_module_admin')
                    ? $container->getParameter('cms_router_module_admin')
                    : [];

                $serviceName = 'cms.router_module.'.strtolower($moduleName).'.admin';

                $cms_router_module_admin[$moduleName] = $serviceName;
                $container->setParameter('cms_router_module_admin', $cms_router_module_admin);

                $container->setDefinition($serviceName, $definition);
            }
        }

        // ================================================

        /** @var \Monolith\CMSBundle\Module\ModuleBundle $moduleBundle */
        foreach (ModuleManager::getModulesPaths($container->getParameter('kernel.bundles_metadata')) as $moduleName => $modulePath) {
            // Обработка routing.yml
            /*
            $routingConfig = $modulePath.'/Resources/config/routing.yml';
            if (file_exists($routingConfig) and is_array(Yaml::parse(file_get_contents($routingConfig)))) {
                $definition = new Definition(
                    'Symfony\\Component\\Routing\\Router', [
                        new Reference('routing.loader'),
                        $routingConfig, [
                            'cache_dir' => $container->getParameter('kernel.cache_dir').'/monolith_cms',
                            'debug'     => $container->getParameter('kernel.debug'),
                            'matcher_cache_class'   => 'CMSModule'.$moduleName.'UrlMatcher',
                            'generator_cache_class' => 'CMSModule'.$moduleName.'UrlGenerator',
                        ],
                    ]
                );
                $definition->addTag('cms_router_module');
                $definition->setPublic(true);

                $container->setDefinition('cms.router_module.'.strtolower($moduleName), $definition);
            }
            */

            // Обработка routing_admin.yml
            /*
            $routingConfig = $modulePath.'/Resources/config/routing_admin.yml';
            if (file_exists($routingConfig) and is_array(Yaml::parse(file_get_contents($routingConfig)))) {
                $definition = new Definition(
                    'Symfony\\Component\\Routing\\Router', [
                        new Reference('routing.loader'),
                        $routingConfig, [
                            'cache_dir' => $container->getParameter('kernel.cache_dir').'/monolith_cms',
                            'debug'     => $container->getParameter('kernel.debug'),
                            'matcher_cache_class'   => 'CMSModule'.$moduleName.'AdminUrlMatcher',
                            'generator_cache_class' => 'CMSModule'.$moduleName.'AdimnUrlGenerator',
                        ],
                    ]
                );
                $definition->addTag('cms_router_module_admin');
                $definition->setPublic(true);

                // Сохранение списка сервисов маршрутов, чтобы можно было быстро перебрать их на название роутов.
                $cms_router_module_admin = $container->hasParameter('cms_router_module_admin')
                    ? $container->getParameter('cms_router_module_admin')
                    : [];

                $serviceName = 'cms.router_module.'.strtolower($moduleName).'.admin';

                $cms_router_module_admin[$moduleName] = $serviceName;
                $container->setParameter('cms_router_module_admin', $cms_router_module_admin);

                $container->setDefinition($serviceName, $definition);
            }
            */

            // Обработка routing_api.yml
            $routingConfig = $modulePath.'/Resources/config/routing_api.yml';
            if (file_exists($routingConfig) and is_array(Yaml::parse(file_get_contents($routingConfig)))) {
                $definition = new Definition(
                    'Symfony\\Component\\Routing\\Router', [
                        new Reference('routing.loader'),
                        $routingConfig, [
                            'cache_dir' => $container->getParameter('kernel.cache_dir').'/monolith_cms',
                            'debug'     => $container->getParameter('kernel.debug'),
                            'matcher_cache_class'   => 'CMSModule'.$moduleName.'ApiUrlMatcher',
                            'generator_cache_class' => 'CMSModule'.$moduleName.'ApiUrlGenerator',
                        ],
                    ]
                );
                $definition->addTag('cms_router_module_api');
                $definition->setPublic(true);

                $container->setDefinition('cms.router_module_api.'.strtolower($moduleName), $definition);
            }
        }
    }
}
