<?php

namespace Smart\CoreBundle\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Inspired by KnpRadBundle
 */
interface ConfigurableBundleInterface extends BundleInterface
{
    /**
     * Configure the root node of a given application using the
     * RadBundle.
     *
     * @param NodeParentInterface $rootNode
     */
    public function buildConfiguration(NodeParentInterface $rootNode);

    /**
     * Configure the container. Replace the standard bundle extension load method.
     *
     * @param array            $config,   The parsed config
     * @param ContainerBuilder $container
     */
    public function buildContainer(array $config, ContainerBuilder $container);
}
