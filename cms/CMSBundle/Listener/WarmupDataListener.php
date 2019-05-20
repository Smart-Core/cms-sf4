<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Listener;

use Monolith\CMSBundle\CMSBundle;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class WarmupDataListener
{
    use ContainerAwareTrait;

    /** @var bool */
    protected $debug;

    /** @var string */
    protected $cacheDir;

    /**
     * WarmupDataListener constructor.
     *
     * @param ContainerInterface $container
     * @param string             $cacheDir
     * @param bool               $debug
     */
    public function __construct(ContainerInterface $container, string $cacheDir, bool $debug)
    {
        $this->container = $container; // @todo remove

        $this->cacheDir = $cacheDir;
        $this->debug    = $debug;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws \Exception
     */
    public function onRequest(GetResponseEvent $event): void
    {
        if ($this->debug and HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $lockFile = $this->cacheDir.'/'.CMSBundle::LOCK_FILE;

            if (file_exists($lockFile)) {
                $this->container->get('cms.site')->init();
                $this->container->get('cms.security')->warmupDatabase();
                $this->container->get('cms.security')->checkDefaultUserGroups();

                unlink($lockFile);
            }
        }
    }
}
