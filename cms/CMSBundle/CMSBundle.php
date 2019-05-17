<?php

declare(strict_types=1);

namespace Monolith\Bundle\CMSBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CMSBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getThemeDir()
    {
        //$currentTheme = $this->container->get('cms.context')->getSite()->getTheme();

        //return $this->container->getParameter('kernel.project_dir').'/themes/'.$currentTheme;
    }
}
