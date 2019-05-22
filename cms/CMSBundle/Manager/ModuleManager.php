<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Manager;

use Monolith\CMSBundle\CMSKernel;
use Monolith\CMSBundle\Controller\AbstractNodeController;
use Monolith\CMSBundle\Module\ModuleBundle;
use Monolith\CMSBundle\Module\ModuleBundleInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ModuleManager
{
    /**
     * @var \AppKernel
     */
    protected $kernel;

    /**
     * @var \Monolith\CMSBundle\Module\ModuleBundle[]
     */
    protected $modules = [];

    /**
     * ModuleManager constructor.
     *
     * @param CMSKernel $kernel
     */
    public function __construct(CMSKernel $kernel)
    {
        $this->kernel  = $kernel;

        foreach ($kernel->getBundles() as $name => $object) {
            if ($object instanceof ModuleBundleInterface) {
                $this->modules[$name] = $object;
            }
        }
    }

    /**
     * Получение списка всех модулей.
     *
     * @return \Monolith\CMSBundle\Module\ModuleBundle[]
     */
    public function all(): array
    {
        return $this->modules;
    }

    /**
     * Получение бандла модуля.
     *
     * @param string $name
     *
     * @return \Monolith\CMSBundle\Module\ModuleBundle|null
     */
    public function get(string $name): ?ModuleBundle
    {
        return isset($this->modules[$name]) ? $this->modules[$name] : null;
    }

    /**
     * Получение списка всех контроллеров
     *
     * @param string $name
     *
     * @return array
     */
    public function getControllers(string $name): array
    {
        $module = $this->get($name);

        $namespace = $module->getNamespace();
        $path = $module->getPath();

        $finder = new Finder();
        $finder->name('*Controller.php');

        $controllers = [];

        /** @var SplFileInfo $file */
        foreach ($finder->in($path.'/Controller') as $file) {
            $controller = substr($file->getRelativePathname(), 0, -4);

            try {
                $reflected = new \ReflectionClass($namespace.'\\Controller\\'.$controller);
            } catch (\ReflectionException $e) {
                continue;
            }

            $parentClass = $reflected->getParentClass();

            if ($parentClass and $parentClass->getName() == AbstractNodeController::class) {
                $controllers[$controller] = [
                    'controller' => $namespace.'\\Controller\\'.$controller,
                    'params' => [],
                ];

                $defaultProperties = $reflected->getDefaultProperties();

                foreach ($reflected->getProperties() as $val) {
                    if ($val->isPublic()) {
                        $controllers[$controller]['params'][$val->getName()] = $defaultProperties[$val->getName()];
                    }
                }
            }
        }

        return $controllers;
    }

    /**
     * @return array
     */
    public function getAllModulesControllersForForm(): array
    {
        $modules = [];
        foreach ($this->modules as $module_name => $module) {
            if ($module->isEnabled()) {
                $modules[$module->getTitle()] = $module_name;
            }
        }

        return $modules;
    }

    /**
     * Проверить, подключен ли модуль.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name): bool
    {
        return isset($this->modules[$name]) ? true : false;
    }

    /**
     * Получение списка модулей с путями на основе данных 'kernel.bundles_metadata'
     *
     * @param array $bundles_metadata
     *
     * @return array
     * @throws \ReflectionException
     */
    static public function getModulesPaths(array $bundles_metadata): array
    {
        $modulesPaths = [];
        foreach ($bundles_metadata as $class => $meta) {
            $reflection = new \ReflectionClass($meta['namespace'].'\\'.$class);

            if (in_array(ModuleBundleInterface::class, $reflection->getInterfaceNames())) {

                $modulesPaths[substr($class, 0, -12)] = $meta['path'];
            }
        }

        return $modulesPaths;
    }
}
