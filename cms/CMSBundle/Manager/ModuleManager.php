<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Manager;

use Monolith\CMSBundle\CMSKernel;
use Monolith\CMSBundle\Module\ModuleBundle;
use Monolith\CMSBundle\Module\ModuleBundleInterface;

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
