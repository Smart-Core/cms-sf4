<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cms');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('cms');
        }

        $rootNode
            ->children()
                ->scalarNode('cache_provider')->defaultValue('cms_cache_pool.filesystem')->end()
                ->arrayNode('design')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('admin_login_logo')
                            ->defaultValue('<a href="/" title=""><b>Monolith</b> CMS</a>')
                            //->info()
                        ->end()

                        ->scalarNode('admin_logo')
                            ->defaultValue('<!-- mini logo for sidebar mini 50x50 pixels -->
                                <span class="logo-mini"><b>C</b>MS</span>
                                <!-- logo for regular state and mobile devices -->
                                <span class="logo-lg"><b>Monolith</b> CMS</span>')
                            //->info()
                        ->end()

                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
