<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Router;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;

class NodeRouter extends Router
{
    /** @var RequestStack */
    protected $request_stack;

    /** @var string */
    protected $rootHash = 'kksdg7724tkshdfvI6734khvsdfKHvdf74';

    /**
     * @param mixed $request_stack
     *
     * @return $this
     */
    public function setRequestStack($request_stack): self
    {
        $this->request_stack = $request_stack;

        return $this;
    }

    /**
     * В случае, если в пути маршрута есть паттерн {_folderPath}, то пробуем подставить его из $parameters или атрибута _route_params.
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        // Метод getDeclaredRouteData() генерируется через Monolith\CMSBundle\Router\PhpGeneratorDumper
        // этот код работает чуть-чуть быстрее примерно на 10%

        // $declaredRouteData = $this->getGenerator()->getDeclaredRouteData($name);
        // if (isset($declaredRouteData[0][0]) and in_array('_folderPath', $declaredRouteData[0])) {

        $route = $this->getRouteCollection()->get($name);

        if (!empty($route) and $route->hasRequirement('_folderPath')) {
            if (isset($parameters['_folderPath'])) {
                // Удаление последнего слеша
                if (mb_substr($parameters['_folderPath'], -1) == '/') {
                    $parameters['_folderPath'] = mb_substr($parameters['_folderPath'], 0, mb_strlen($parameters['_folderPath']) - 1);
                }

                // Удаление первого слеша
                if (mb_substr($parameters['_folderPath'], 0, 1) == '/') {
                    $parameters['_folderPath'] = mb_substr($parameters['_folderPath'], 1);
                }
            }

            $routeParams = $this->request_stack->getCurrentRequest()->attributes->get('_route_params', null);

            if (isset($routeParams['_folderPath']) and (!isset($parameters['_folderPath']) or empty($parameters['_folderPath']))) {
                $parameters['_folderPath'] = empty($routeParams['_folderPath']) ? $this->rootHash : $routeParams['_folderPath'];
            }
        }

        return str_replace($this->rootHash.'/', '', $this->getGenerator()->generate($name, $parameters, $referenceType));
    }
}
