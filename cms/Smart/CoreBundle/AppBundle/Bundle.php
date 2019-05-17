<?php

namespace Smart\CoreBundle\AppBundle;

use Smart\CoreBundle\DependencyInjection\Compiler;
use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;

/**
 * Inspired by KnpRadBundle
 */
class Bundle extends BaseBundle implements ConfigurableBundleInterface
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new Compiler\RemoveUnavailableServicesPass);
        $container->addCompilerPass(new Compiler\RegisterTwigExtensionsPass($this));
    }

    public function getContainerExtension()
    {
        if ($extension = parent::getContainerExtension()) {
            return $extension;
        }

        return $this->extension = new ContainerExtension($this);
    }

    public function buildConfiguration(NodeParentInterface $rootNode)
    {
    }

    public function buildContainer(array $config, ContainerBuilder $container)
    {
    }
}
