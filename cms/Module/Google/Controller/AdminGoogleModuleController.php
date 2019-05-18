<?php

namespace Monolith\Module\Google\Controller;

use Smart\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminGoogleModuleController extends Controller
{
    public function indexAction(Request $request)
    {

        return $this->render('GoogleModuleBundle:Admin:index.html.twig', []);
    }
}
