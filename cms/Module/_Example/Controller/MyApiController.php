<?php

declare(strict_types=1);

use Monolith\CMSBundle\Controller\AbstractModuleApiController;
use Monolith\CMSBundle\Entity\Node;

/**
 * API интерфейс
 *
 * У цмс своя точка входа в апи, например:
 * http://site.com/api/v1/
 *
 * Если модуль называется Example, то его маршрут будет
 *
 * http://site.com/api/v1/example/
 *
 * @CmsApiContoller
 */
class MyApiController extends AbstractApiController
{
    /**
     * Метод контроллера по умолчанию
     *
     * GET http://site.com/api/v1/example/
     *
     * Ниже приведены аналогичные варианты, но если больше одной аннотации, то выбрасывать исключение.
     * @CmsRoute()
     * @CmsRoute("/", name="some_index")
     * @CmsRouteDefault(name="some_index")
     * @CmsRouteDefault
     * @CmsRouteGet
     */
    public function letsDoIt(Node $node) {}

    /**
     * GET http://site.com/api/v1/example/items/list
     *
     * @CmsRoute("/items/list", method="GET", name="example_api_items_list")
     */
    public function test() {}

    /**
     * Произвольный маршрут с параметрами
     *
     * POST http://site.com/api/v1/example/345/
     *
     * @CmsRoutePost("/{page}/", requirements={"page"="\d+"})
     * @CmsRoutePost("/{page<\d+>}")
     * @CmsRoutePost("/{page<\d+>?1}", name="show_list")
     */
    public function update(int $page = 1) {}

}
