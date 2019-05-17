<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CmsController extends AbstractController
{
    /**
     * @Route("/{slug<.+>}", name="cms_index", methods={"GET"})
     */
    public function index(Request $request, string $slug = '', array $options = null)
    {
        return $this->render('hello/index.html.twig', [
            'controller_name' => 'CmsController:'.$slug,
        ]);
    }
}
