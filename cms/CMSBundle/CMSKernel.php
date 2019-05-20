<?php

declare(strict_types=1);

namespace Monolith\CMSBundle;

use Monolith\CMSBundle\Module\ModuleBundle;
use Symfony\Component\HttpKernel\Kernel;

if (!defined('START_TIME')) {
    define('START_TIME', microtime(true));
}
if (!defined('START_MEMORY')) {
    define('START_MEMORY', memory_get_usage());
}

abstract class CMSKernel extends Kernel
{
    /**
     * Initializes bundles.
     *
     * @throws \LogicException if two bundles share a common name
     */
    protected function initializeBundles()
    {
        parent::initializeBundles();

        \Profiler::setKernel($this);

        $this->registerCmsModules($this->bundles);
    }

    /**
     * Добавление модулей в список бандлов.
     *
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface[] $bundles
     *
     * @throws \ReflectionException
     */
    protected function registerCmsModules(&$bundles): void
    {
        if (empty($bundles)) {
            return;
        }

        $modulesConfig = $this->getProjectDir().'/config/cms_modules.ini';

        if (file_exists($modulesConfig)) {
            foreach (parse_ini_file($modulesConfig) as $module_class => $is_enabled) {
                if (class_exists($module_class)) {
                    /** @var \Monolith\CMSBundle\Module\ModuleBundle $module_bundle */
                    $module_bundle = new $module_class();

                    if ($module_bundle instanceof ModuleBundle) {
                        $module_bundle->setIsEnabled((bool) $is_enabled);
                        $this->modules[$module_bundle->getName()] = $module_bundle;
                        $bundles[$module_bundle->getName()] = $module_bundle;
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
