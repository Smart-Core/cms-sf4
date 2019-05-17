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
use Symfony\Component\Finder\Finder;
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

        $this->registerMonolithCmsBundles($bundles);
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
     * @param string $name
     *
     * @return \Monolith\CMSBundle\Module\ModuleBundle|null
     */
    public function getModule(string $name): ?ModuleBundle
    {
        if (isset($this->modules[$name])) {
            return $this->modules[$name];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getSiteName(): string
    {
        return $this->siteName;
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

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface[] $bundles
     */
    protected function registerMonolithCmsBundles(&$bundles)
    {
        //$this->registerCmsDependencyBundles($bundles);
        //$this->autoRegisterSiteBundle($bundles);

        if ($bundles) {
            $this->registerCmsModules($bundles);
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface[] $bundles
     */
    protected function registerCmsModules(&$bundles): void
    {
        $reflector = new \ReflectionClass(end($bundles)); // Регистрация модулей строго после регистрции SiteBundle
        $modulesConfig = dirname($reflector->getFileName()).'/Resources/config/modules.ini';

        if (file_exists($modulesConfig)) {
            foreach (parse_ini_file($modulesConfig) as $module_class => $is_enabled) {
                if (class_exists($module_class)) {
                    /** @var \Monolith\CMSBundle\Module\ModuleBundle $module_bundle */
                    $module_bundle = new $module_class();

                    if ($module_bundle instanceof ModuleBundle) {
                        $module_bundle->setIsEnabled((bool) $is_enabled);
                        $this->modules[$module_bundle->getName()] = $module_bundle;
                        $bundles[] = $module_bundle;
                    } else {
                        throw new \Exception($module_class.' is not instanceof '.ModuleBundle::class);
                    }
                } else {
                    throw new \Exception($module_class.' is not exists.');
                }
            }
        }
    }
}
