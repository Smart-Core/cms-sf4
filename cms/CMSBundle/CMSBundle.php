<?php

declare(strict_types=1);

namespace Monolith\CMSBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CMSBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        //$container->setParameter('container.build_id', '39thod7hkdfg973rf'); // Фикс с версии symfony v3.4.3
    }

    public function getThemeDir()
    {
        //$currentTheme = $this->container->get('cms.context')->getSite()->getTheme();

        //return $this->container->getParameter('kernel.project_dir').'/themes/'.$currentTheme;
    }
}
