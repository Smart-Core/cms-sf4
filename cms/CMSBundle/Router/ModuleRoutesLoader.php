<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Router;

use Monolith\CMSBundle\Manager\ModuleManager;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Routing\RouteCollection;

/**
 * Загрузчик маршрутов модулей.
 *
 * @deprecated теперь ноды разруливаются через аннотации.
 */
class ModuleRoutesLoader extends Loader implements LoaderInterface
{
    use ContainerAwareTrait;

    /**
     * @var bool
     *
     * Route is loaded
     */
    private $loaded = false;

    /**
     * Loads a resource.
     *
     * @param mixed  $resource The resource
     * @param string $type     The resource type
     *
     * @return RouteCollection
     *
     * @throws \RuntimeException Loader is added twice
     */
    public function load($resource, $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $collection = new RouteCollection();

        foreach (ModuleManager::getModulesPaths($this->container->getParameter('kernel.bundles_metadata')) as $__moduleName => $modulePath) {
            $resource = $modulePath.'/Resources/config/routing.yml';
            if (file_exists($resource)) {
                /** @var \Symfony\Component\Routing\RouteCollection $importedRoutes */
                $importedRoutes = $this->import($modulePath.'/Resources/config/routing.yml', 'yaml');
                $importedRoutes->addPrefix(
                    '/{_folderPath}/',
                    ['_folderPath' => ''],
                    ['_folderPath' => '.*']
                );

                $collection->addCollection($importedRoutes);
            }
        }

        return $collection;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return bool true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null): bool
    {
        return 'modules_enabled' === $type;
    }
}
