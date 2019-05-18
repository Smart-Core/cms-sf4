<?php

declare(strict_types=1);

namespace Monolith\Module\User\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Перегрузка шаблона логина.
 */
class SecurityController extends BaseSecurityController
{
    protected $data = [];

    public function loginAction(Request $request, array $data = [])
    {
        $this->data = $data;

        return parent::loginAction($request);
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('@UserModule/Security/login.html.twig', $data + $this->data);
    }
}
