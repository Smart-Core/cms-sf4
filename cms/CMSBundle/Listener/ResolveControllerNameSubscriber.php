<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Listener;

use Monolith\CMSBundle\Manager\NodeManager;
use Monolith\CMSBundle\Module\ModuleBundle;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Bundle\FrameworkBundle\EventListener\ResolveControllerNameSubscriber as BaseResolveControllerNameSubscriber;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class ResolveControllerNameSubscriber extends BaseResolveControllerNameSubscriber
{
    /** @var KernelInterface */
    protected $kernel;

    /** @var NodeManager */
    protected $nodeManager;

    /** @var ControllerNameParser */
    protected $parser;

    /**
     * ResolveControllerNameSubscriber constructor.
     *
     * @param ControllerNameParser $parser
     * @param NodeManager          $nodeManager
     * @param KernelInterface      $kernel
     */
    public function __construct(ControllerNameParser $parser, NodeManager $nodeManager, KernelInterface $kernel)
    {
        $this->kernel      = $kernel;
        $this->nodeManager = $nodeManager;
        $this->parser      = $parser;

        parent::__construct($parser);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 24],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        $controller = $event->getRequest()->attributes->get('_controller');

        $parts = explode(':', (string) $controller);

        if (isset($parts[0]) and class_exists($parts[0])) {
            // Запрет на отключенные модули, вызываемые по прямым маршрутам.
            $bundle = $this->getBundleFromController($controller);

            if ($bundle instanceof ModuleBundle and !$bundle->isEnabled()) {
                $event->setResponse(new Response('Module '.$bundle->getShortName().' is disabled.', 403));
            }
        }

        if (is_numeric($parts[0])) {
            $parts[0] = intval($parts[0]);

            $node = $this->nodeManager->get($parts[0]);

            if (is_null($node)) {
                return;
            }

            $event->getRequest()->attributes->set('_node', $node);

            $controller = $node->getController();

            if (class_exists($controller)) {
                $event->getRequest()->attributes->set('_controller', $node->getController().'::index'); // @todo сделать поддержку __invoke

                foreach ($node->getParams() as $param => $val) {
                    $event->getRequest()->attributes->set($param, $val);
                }

                foreach ($node->getParamsOverride() as $param => $value) {
                    $event->getRequest()->attributes->set($param, $value);
                }
            } else {
                throw new \Exception('Controller class does not exist: '.$controller);


                /**
                 * @deprecated !!! Старый код, когда контроллер хранился в формате: MyBundle:Edit:index
                 */
                /*
                $controllerName = isset($parts[1]) ? $parts[1] : null;
                $actionName = isset($parts[2]) ? $parts[2] : 'index';

                if (!empty($node->getControllerParams())) {
                    foreach ($node->getControllerTemp($controllerName, $actionName) as $key => $value) {
                        $event->getRequest()->attributes->set($key, $value);
                    }
                } else {
                    $controllerName = $node->getModule().':'.$node->getController();

                    // Запрет на отключенные модули, вызываемые через ноды.
                    $bundle = $this->getBundleFromController($this->parser->parse($controllerName));
                    if ($bundle instanceof ModuleBundle and !$bundle->isEnabled()) {
                        $event->setResponse(new Response('Module '.$bundle->getShortName().' is disabled.', 403));

                        return;
                    }

                    $event->getRequest()->attributes->set('_controller', $controllerName);

                    foreach ($node->getParams() as $param => $val) {
                        $event->getRequest()->attributes->set($param, $val);
                    }
                }
                */
            }
        }

        parent::onKernelRequest($event);
    }

    /**
     * @param string $controller
     *
     * @return \Symfony\Component\HttpKernel\Bundle\BundleInterface
     */
    protected function getBundleFromController(string $controller): ?BundleInterface
    {
        if (0 === preg_match('#^(.*?\\\\Controller\\\\(.+)Controller)::(.+)Action$#', $controller, $match)) {
            if (0 === preg_match('#^(.*?\\\\Controller\\\\(.+)Controller)::(.+)$#', $controller, $match)) {
                throw new \InvalidArgumentException(sprintf('The "%s" controller is not a valid "class::method" string.', $controller));
            }
        }

        $className = $match[1];
        //$controllerName = $match[2];
        //$actionName = $match[3];
        foreach ($this->kernel->getBundles() as $name => $bundle) {
            if (0 !== strpos($className, $bundle->getNamespace())) {
                continue;
            }

            return $bundle;
        }

        return null;
        //throw new \InvalidArgumentException(sprintf('Unable to find a bundle that defines controller "%s".', $controller));
    }
}
