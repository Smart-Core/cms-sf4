<?php

namespace Smart\CoreBundle\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigurationFactory
{
    public function createConfiguration(ConfigurableBundleInterface $bundle, array $config, ContainerBuilder $container)
    {
        return new Configuration($bundle);
    }
}
