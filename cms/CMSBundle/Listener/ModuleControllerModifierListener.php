<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Listener;

use Monolith\CMSBundle\Controller\AbstractNodeController;
use Monolith\CMSBundle\Manager\ContextManager;
use Monolith\CMSBundle\Manager\FolderManager;
use Monolith\CMSBundle\Manager\ModuleManager;
use Monolith\CMSBundle\Manager\NodeManager;
use Monolith\CMSBundle\Twig\Loader\FilesystemLoader;
use SmartCore\Bundle\SettingsBundle\Manager\SettingsManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ModuleControllerModifierListener
{
    /** @var ContextManager */
    protected $contextManager;

    /** @var FolderManager */
    protected $folderManager;

    /** @var ModuleManager */
    protected $moduleManager;

    /** @var NodeManager */
    protected $nodeManager;

    /** @var SettingsManager  */
    protected $settingsManager;

    /** @var \Monolith\CMSBundle\Twig\Loader\FilesystemLoader  */
    protected $twigLoader;

    /**
     * ModuleControllerModifierListener constructor.
     *
     * @param ContextManager   $contextManager
     * @param FolderManager    $folderManager
     * @param ModuleManager    $moduleManager
     * @param NodeManager      $nodeManager
     * @param SettingsManager  $settingsManager
     * @param FilesystemLoader $twigLoader
     */
    public function __construct(
        ContextManager $contextManager,
        FolderManager $folderManager,
        ModuleManager $moduleManager,
        NodeManager $nodeManager,
        SettingsManager $settingsManager,
        FilesystemLoader $twigLoader
    ) {
        $this->contextManager   = $contextManager;
        $this->folderManager    = $folderManager;
        $this->moduleManager    = $moduleManager;
        $this->nodeManager      = $nodeManager;
        $this->settingsManager  = $settingsManager;
        $this->twigLoader       = $twigLoader;
    }

    public function onView(GetResponseForControllerResultEvent $event): void
    {
        $response = new Response();
        $response->setContent($event->getControllerResult());

        $event->setResponse($response);
    }

    public function onController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        $request = $event->getRequest();

        if ($request->attributes->has('_node')) {
            /** @var $node \Monolith\CMSBundle\Entity\Node */
            $node = $request->attributes->get('_node');

            /** @var AbstractNodeController $controllerObj */
            $controllerObj = $controller[0];
            if ($controllerObj instanceof AbstractNodeController) {
                foreach ($node->getParams() as $param => $val) {
                    $controllerObj->$param = $val;
                }

                $controllerObj->setNode($node);
            }

            // @todo сделать поддержку кириллических путей.
            $folderPath = substr(str_replace($request->getBaseUrl(), '', $this->folderManager->getUri($node)), 1);

            if (false !== strrpos($folderPath, '/', strlen($folderPath) - 1)) {
                $folderPath = substr($folderPath, 0, strlen($folderPath) - 1);
            }

            //$routeParams = $node->getControllerParams();
            $routeParams = [];
            $routeParams['_folderPath'] = $folderPath;

            $request->attributes->set('_route_params', $routeParams);
            $request->attributes->remove('_node');

            $this->twigLoader->setModuleTheme($node);

            $this->contextManager->setCurrentNodeId($node->getId());
        }
    }

    public function onRequest(GetResponseEvent $event): void
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            try {
                date_default_timezone_set($this->settingsManager->get('cms:timezone'));
            } catch (\Exception $e) {
                date_default_timezone_set('Europe/Moscow');
            }
        }
    }

    public function onResponse(FilterResponseEvent $event): void
    {
        $this->contextManager->setCurrentNodeId(null);
        $this->twigLoader->setModuleTheme(null);
    }
}
