<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class AdminController extends AbstractController
{
    /**
     * @Route("/%admin_path%/", name="cms_admin.index", methods={"GET"})
     */
    public function index(string $slug = '')
    {
        if (!$this->get('cms.security')->isGranted('ROLE_ADMIN')) {
            return $this->render('@CMS/Admin/login.html.twig');
        }

        return $this->render('@CMS/Admin/dashboard.html.twig', [
            'controller_name' => 'Admin Controller:'.$slug,
        ]);
    }

    /**
     * Render Elfinder FileManager.
     *
     * @return Response
     */
    public function elfinderAction(): Response
    {
        return $this->render('@CMS/Admin/elfinder.html.twig', [
            'fullscreen'    => true,
            'includeAssets' => $this->container->getParameter('fm_elfinder')['instances']['default']['include_assets'],
            'prefix'        => $this->container->getParameter('fm_elfinder')['assets_path'],
            'theme'         => $this->container->getParameter('fm_elfinder')['instances']['default']['theme'],
        ]);
    }
}
