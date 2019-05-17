<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/%admin_path%/", name="cms_admin_index", methods={"GET"})
     */
    public function indexAction(Request $request, string $slug = '', array $options = null)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->render('@CMS/Admin/login.html.twig');
        }

        return $this->render('hello/index.html.twig', [
            'controller_name' => 'Admin Controller:'.$slug,
        ]);
    }
}
