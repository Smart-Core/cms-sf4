<?php

declare(strict_types=1);

namespace Monolith\CMSBundle;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Monolith\CMSBundle\Module\ModuleBundle;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

abstract class CMSKernel extends Kernel
{
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
     */
    protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, $class, $baseClass)
    {
        parent::dumpContainer($cache, $container, $class, $baseClass);

        /** @var ContainerInterface $container */
        $container = require $cache->getPath();
        $container->set('kernel', $this);

        $container->get('settings')->warmupDatabase();

        $container->get('cms.site')->init();

        // @todo убрать в другое место, потому что зависит от сайта
        $container->get('cms.region')->checkForDefault();

        try {
            $container->get('cms.security')->warmupDatabase();
            $container->get('cms.security')->checkDefaultUserGroups();
        } catch (TableNotFoundException $e) {
            // @todo
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface[] $bundles
     *
     * @throws \ReflectionException
     */
    protected function registerCmsModules(&$bundles): void
    {
        if (empty($bundles)) {
            return;
        }

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
