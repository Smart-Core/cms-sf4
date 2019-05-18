<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller;

use Monolith\CMSBundle\Entity\Node;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CmsController extends AbstractController
{
    /**
     * @Route("/", name="cms_index", methods={"GET"})
     * Route("/{slug<.+>}", name="cms_index", methods={"GET"})
     */
    public function index(Request $request, string $slug = '', array $options = null)
    {
        return $this->render('hello/index.html.twig', [
            'controller_name' => 'CmsController:'.$slug,
        ]);
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
     *
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
}
