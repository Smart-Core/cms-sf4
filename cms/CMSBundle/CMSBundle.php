<?php

declare(strict_types=1);

namespace Monolith\CMSBundle;

use Monolith\CMSBundle\DependencyInjection\Compiler\ChangeRouterClassPass;
use Monolith\CMSBundle\DependencyInjection\Compiler\DefaultRegionCreatorPass;
use Monolith\CMSBundle\DependencyInjection\Compiler\DeprecationsFixesCompilerPass;
use Monolith\CMSBundle\DependencyInjection\Compiler\FormPass;
use Monolith\CMSBundle\DependencyInjection\Compiler\LiipThemeLocatorsPass;
use Monolith\CMSBundle\DependencyInjection\Compiler\ModulesRoutingResolverPass;
use Monolith\CMSBundle\DependencyInjection\Compiler\WarmupDataPass;
use Monolith\CMSBundle\DependencyInjection\Compiler\TwigLoaderPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CMSBundle extends Bundle
{
    const LOCK_FILE = 'cms_need_warmap_db.lock';

    public function boot(): void
    {
        Container::setContainer($this->container);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        //$container->setParameter('container.build_id', '39thod7hkdfg973rf'); // Фикс с версии symfony v3.4.3

        $container->addCompilerPass(new ChangeRouterClassPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
        $container->addCompilerPass(new TwigLoaderPass());
        $container->addCompilerPass(new LiipThemeLocatorsPass());
        $container->addCompilerPass(new ModulesRoutingResolverPass());
        $container->addCompilerPass(new FormPass());
        $container->addCompilerPass(new DeprecationsFixesCompilerPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new WarmupDataPass());
    }

    public function getThemeDir() // @todo убрать
    {
        if (empty($this->container->get('cms.context')->getSite())) {
            $currentTheme = 'default';
        } else {
            $currentTheme = $this->container->get('cms.context')->getSite()->getTheme();
        }

        return $this->container->getParameter('kernel.project_dir').'/themes/'.$currentTheme;
    }
}
