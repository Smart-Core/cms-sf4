<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller;

use Monolith\CMSBundle\Entity\Node;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Twig\Error\LoaderError;

class CmsController extends AbstractController
{
    /**
     * @Route("/{slug<.+>}", name="cms_index", methods={"GET"})
     */
    public function index(Request $request, string $slug = '', array $options = null)
    {
        $cmsContext = $this->get('cms.context');
        $twig       = $this->get('twig');
        $profiler   = $this->get('profiler');

        // Кеширование роутера.
        $cache_key = md5('site_id='.$cmsContext->getSiteId().'cms_router='.$request->getBaseUrl().$slug);

        $cmsContext->stopwatchStart('cms_router');

        if (null === $router_data = $this->get('cms.cache')->get($cache_key)) {
            $router_data = $this->get('cms.router')->match($request->getBaseUrl(), $slug, HttpKernelInterface::MASTER_REQUEST, $options);

            $this->get('cms.cache')->set($cache_key, $router_data, ['folder', 'node']);
        }

        $profiler->get('cms')->setRouterData($router_data);

        $cmsContext->stopwatchStop('cms_router');

        if ($router_data['status'] == 301 and $router_data['redirect_to']) {
            return new RedirectResponse($router_data['redirect_to'], $router_data['status']);
        }

        if (empty($router_data['folders'])) { // Случай пустой инсталляции, когда еще ни одна папка не создана.
            $this->get('cms.toolbar')->prepare();

            return $twig->render('@CMS/welcome.html.twig');
        }

        $cmsContext->setTemplate($router_data['template']);

        if (!$this->get('cms.security')->checkForFoldersRouterData($router_data['folders'], 'read')) {
            $router_data['status'] = 403;
        }

        if ($router_data['status'] == 404) {
            $this->get('monolog.logger.request')->error('Page not found: '.$request->getUri());

            throw new NotFoundHttpException('Page not found.');
        } elseif ($router_data['status'] == 403) {
            throw new AccessDeniedHttpException('Access Denied.');
        }

        $this->get('html')->setMetas($router_data['meta']);

        foreach ($router_data['folders'] as $folderId => $folderData) { // @todo учёт локали
            $this->get('cms.breadcrumbs')->add($this->get('cms.folder')->getUri($folderId), $folderData['title'], $folderData['description']);
        }

        $cmsContext->setCurrentFolderId($router_data['current_folder_id']);
        $cmsContext->setCurrentFolderPath($router_data['current_folder_path']);

        // Список нод кешируется только при GET запросах.
        $router_data['http_method'] = $request->getMethod();

        $nodes = $this->get('cms.node')->buildList($router_data);

        $profiler->get('cms')->setNodes($nodes);

        \Profiler::start('Build Modules Data');
        // Разложенные по областям, отрендеренные ноды
        $nodesResponses = $this->get('cms.node')->buildModulesData($request, $nodes);
        \Profiler::end('Build Modules Data');

        if ($nodesResponses instanceof Response) {
            return $nodesResponses;
        }

        $this->get('cms.toolbar')->prepare($this->get('cms.node')->getFrontControls());

        try {
            return $twig->render($cmsContext->getTemplate().'.html.twig', $nodesResponses);
        } catch (LoaderError $e) {
            if ($this->get('kernel')->isDebug()) {
                return $twig->render('@CMS/error.html.twig', ['errors' => [$e->getMessage()]]);
            }
        }

        return $twig->render('@CMS/welcome.html.twig');
    }

    /**
     * Обработчик POST запросов.
     *
     * @param Request $request
     * @param string $slug
     *
     * @return RedirectResponse|Response
     *
     * @Route("/{slug<.+>}", name="cms_post", methods={"POST"})
     *
     * @todo продумать! здесь же происходит "магия" с /admin/login/check
     */
    public function post(Request $request, $slug): Response
    {
        // Получение $node_id
        $data = $request->request->all();
        $node_id = null;
        foreach ($data as $key => $value) {
            if ($key == '_node_id') {
                $node_id = $data['_node_id'];
                unset($data['_node_id']);
                break;
            }

            if (is_array($value) and array_key_exists('_node_id', $value)) {
                $node_id = $data[$key]['_node_id'];
                unset($data[$key]['_node_id']);
                break;
            }
        }

        foreach ($data as $key => $value) {
            $request->request->set($key, $value);
        }

        $node = $this->get('cms.node')->get((int) $node_id);

        if (!$node instanceof Node or !$node->isActive()) {
            throw new AccessDeniedHttpException('Node is not active.');
        }

        // @todo сделать здесь проверку на права доступа, а также доступность ноды в запрошенной папке.

        return $this->forward($node->getId().':'.$node->getController(), ['slug' => $slug]);
    }

    /**
     * @param \Monolith\Bundle\CMSBundle\Entity\Node $node
     *
     * @return Response
     */
    public function moduleNotConfiguredAction(Node $node)
    {
        return new Response('Module "'.$node->getModule().'" not yet configured. Node: '.$node->getId().'<br />');
    }

    /**
     * @param Request $request
     * @param int $node_id
     * @param string $slug
     *
     * @return Response
     */
    public function apiAction(Request $request, $node_id, $slug = null)
    {
        // @todo сделать проверку, доступна ли нода в папке т.к. папка может быть выключенной или пользователь не имеет к ней прав.

        $node = $this->get('cms.node')->get((int) $node_id);

        if (null === $node) {
            return $this->apiNotFoundAction();
        }

        try {
            $controller = $this->get('cms.router')->matchModuleApi($node->getModule(), '/'.$slug, $request);
        } catch (MethodNotAllowedException $e) {
            return new JsonResponse([
                'status'  => 'error',
                'message' => 'MethodNotAllowedException.',
                'data'    => [],
            ], 404);
        }

        if (null === $controller) {
            return $this->apiNotFoundAction();
        }

        $controller['node'] = $node;

        $subRequest = $this->get('request_stack')->getCurrentRequest()->duplicate(
            $request->query->all(),
            $request->request->all(),
            $controller,
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all()
        );

        return $this->get('http_kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * @return JsonResponse
     */
    public function apiNotFoundAction()
    {
        return new JsonResponse([
            'status'  => 'error',
            'message' => 'Некорректный запрос.',
            'data'    => [],
        ], 404);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function switchSelectedSiteAction(Request $request): RedirectResponse
    {
        $site_id = $request->request->get('site', 0);
        $route   = $request->request->get('route', 'cms_admin.index');

        $switcher = $this->get('cms.context')->getSiteSwitcher();

        try {
            $url = $this->generateUrl($route);
        } catch (RouteNotFoundException $e) {
            $url = $this->generateUrl('cms_admin.index');
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $site = $em->getRepository('CMSBundle:Site')->find((int) $site_id);

        if ($site) {
            if (isset($switcher[$site_id])) {
                $url = $switcher[$site_id]['domain'] . $url;
            } else {
                // @todo если не указан домен
                $url = $site->getDomain()->getName() . $url;
            }
        }

        $token = md5(microtime());

        $data = [
            'token'   => $token,
            'user_id' => $this->getUser()->getId(),
        ];

        $this->get('doctrine_cache.providers.cms')->save($token, $data, 3);

        $redirect = $this->redirect('//'.$url.'?switch_site_token='.$token);

        return $redirect;
    }
}
