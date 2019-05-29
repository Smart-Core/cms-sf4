<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Router;

use Monolith\CMSBundle\Manager\ModuleManager;
use Monolith\CMSBundle\Module\ModuleBundle;
use Monolith\CMSBundle\Module\ModuleBundleInterface;
use Symfony\Bundle\FrameworkBundle\Routing\AnnotatedRouteControllerLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

class ModuleAdminRoutesAnnotaionLoader extends AnnotatedRouteControllerLoader implements LoaderInterface
{
    /**
     * @var KernelInterface;
     */
    protected $kernel;

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

        /** @var ModuleBundle $bundle */
        foreach ($this->kernel->getBundles() as $bundleName => $bundle) {
            if (!in_array(ModuleBundleInterface::class, (new \ReflectionClass($bundle))->getInterfaceNames())) {
                continue;
            }

            $controllers = ModuleManager::getAdminControllers(new $bundle);

            foreach ($controllers as $controller) {
                /** @var RouteCollection $importedRoutes */
                $importedRoutes = parent::load($controller['class'], $type);
                $importedRoutes->addPrefix('/'.$bundle->getShortName().'/');

                $collection->addCollection($importedRoutes);
            }
        }

        return $collection;
    }

    /**
     * @param KernelInterface $kernel
     *
     * @return $this
     */
    public function setKernel($kernel): self
    {
        $this->kernel = $kernel;

        return $this;
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
        return 'module_admin_annotation' === $type;
    }
}
