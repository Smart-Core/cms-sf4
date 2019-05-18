<?php

namespace Monolith\Module\Google\Controller;

use Smart\CoreBundle\Controller\Controller;
use Monolith\CMSBundle\Module\NodeTrait;

class GoogleModuleController extends Controller
{
    use NodeTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        return $this->render('GoogleModuleBundle::index.html.twig', []);
    }
}
