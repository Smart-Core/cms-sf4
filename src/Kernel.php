<?php

namespace App;

use Monolith\CMSBundle\Module\ModuleBundle;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }

        //$this->registerMonolithCmsBundles($bundles);
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }

    // ======================================================================================================

    /** @var string  */
    protected $siteName = null;

    /** @var ModuleBundle[] */
    protected $modules = [];

    /**
     * Получить список подключенных модулей CMS.
     *
     * @return \Monolith\CMSBundle\Module\ModuleBundle[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Для Setting Bundle необходимо отслеживать момент сборки контейнера, чтобы синхронизировать настройки из конфигов с БД.
     *
     * @todo сделать наследуемый класс Kernel и перенести туда этот метод.
     */
    protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, $class, $baseClass)
    {
        parent::dumpContainer($cache, $container, $class, $baseClass);

        /** @var ContainerInterface $container */
        $container = require $cache->getPath();
        $container->set('kernel', $this);
        $container->get('settings')->warmupDatabase();
    }

    /**
     * Prepares the ContainerBuilder before it is compiled.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function prepareContainer(ContainerBuilder $container): void
    {
        parent::prepareContainer($container);

        $modulesPaths = [];
        foreach ($this->modules as $module) {
            $modulesPaths[$module->getShortName()] = $module->getPath();
        }

        $container->setParameter('monolith_cms.modules_paths', $modulesPaths);
        $container->setParameter('monolith_cms.site_name', $this->siteName);
    }
}
