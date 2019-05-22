<?php

declare(strict_types=1);

use Monolith\CMSBundle\Controller\AbstractNodeController;
use Monolith\CMSBundle\Entity\Node;

/**
 * Контроллеры, которые могут подключаться в ноду и встраиваться в раскладки макетов.
 *
 * Отвечает по маршрутам вида: http://site.com/some/node/path/, притом нода в этом маршруте
 * может быть подключена веше, например http://site.com/some/
 *
 * @todo придумать как указывать параметры подключения, которые сейчас @NodePropertiesForm
 */
class MyNodeController extends AbstractNodeController
{
    protected $nodePropertiesFormClass = MyNodePropertiesFormType::class;

    // Подумать над вариантом инжектить ноду в контроллер или всёже лучше в экшен?
    protected $_node;

    // Параметра подключения модуля прописываются прямо в свойствах класса
    protected $my_param1 = null;
    protected $my_param2 = 123;
    protected $my_param3 = [];

    public function getDefaultProperties()
    {
        
    }
    
    /**
     * Метод контроллера по умолчанию
     *
     * Ниже приведены аналогичные варианты, но если больше одной аннотации, то выбрасывать исключение.
     * @CmsRoute()
     * @CmsRoute("/", method="GET", name="some_index")
     * @CmsRouteDefault() // Может быть не указано имя маршрута - это нормально, тогда просто собрать автоматом из связки "модуль_контроллер_метод", например "Example_SomeDefaultNode_letsDoIt"
     * @CmsRouteDefault
     * @CmsRouteGet
     */
    public function letsDoIt(Node $node) {}

    /**
     * Обработчик POST по умолчанию.
     *
     * POST http://site.com/some/node/path/
     *
     * @CmsRoute("/", method="POST")
     * @CmsRouteDefaultPost
     */
    public function someActionForPostProcessing() {}

    /**
     * Произвольный маршрут с параметрами
     *
     * GET http://site.com/some/node/path/show/123/
     *
     * Ниже приведены аналогичные варианты:
     * @CmsRoute("/show/{page}/", requirements={"page"="\d+"})
     * @CmsRoute("/show/{page<\d+>}")
     * @CmsRoute("/show/{page<\d+>?1}", name="show_list")
     */
    public function show(int $page = 1) {}

    /**
     * Метод при EIP запросе с фронт-админки
     *
     * GET http://site.com/admin/eip/
     *
     * @CmsRouteEip
     */
    public function defaultEipMethod() {}

    /**
     * Метод для сохранения данных при EIP с фронт-админки
     *
     * POST http://site.com/some/node/path/
     *
     * @CmsRouteEipPost
     */
    public function defaultEipMethodPost() {}

    /**
     * Получение данных для EIP
     *
     * @todo как их получать, если нода с маршрутом?
     *
     * @CmsRouteEipControls
     */
    public function getEipControls() {}

    /**
     * Этим методом происходит встраивание в тулбар, туда можно добавить кнопки, меню, формы и т.д.
     *
     * @CmsRouteToolbar
     */
    public function intergateToToolbar()
    {

    }
}
