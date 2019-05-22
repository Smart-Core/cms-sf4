<?php

declare(strict_types=1);

use Monolith\CMSBundle\Entity\Node;

class MyAdminController extends AbstractAdminController
{
    /**
     * Метод контроллера по умолчанию
     *
     * GET http://site.com/admin/Example/
     *
     * Ниже приведены аналогичные варианты, но если больше одной аннотации, то выбрасывать исключение.
     * @CmsRoute()
     * @CmsRoute("/", name="some_index")
     * @CmsRouteDefault(name="some_index")
     * @CmsRouteDefault
     * @CmsRouteGet
     */
    public function letsDoIt() {}
}
