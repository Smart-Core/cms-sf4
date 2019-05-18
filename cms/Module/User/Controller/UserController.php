<?php

declare(strict_types=1);

namespace Monolith\Module\User\Controller;

use Monolith\CMSBundle\Annotation\NodePropertiesForm;
use Monolith\CMSBundle\Entity\Node;
use Monolith\CMSBundle\Module\NodeTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{
    use NodeTrait;

    /**
     * @param Node $node
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @NodePropertiesForm("NodePropertiesFormType")
     */
    public function indexAction(Node $node)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY') or $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->forward('FOSUserBundle:Profile:show');
        }

        return $this->forward('UserModuleBundle:Security:login', [
            'data' => [
                'allow_password_resetting' => $node->getParam('allow_password_resetting'),
                'allow_registration'       => $node->getParam('allow_registration'),
            ],
            'node_id' => $node->getId(),
        ]);
    }

    public function postAction($slug)
    {
        return $this->forward('CMSBundle:Engine:run', ['slug' => $slug]);
    }

    public function editAction()
    {
        $this->get('cms.breadcrumbs')->add('edit', 'Редактирование');

        return $this->forward('FOSUserBundle:Profile:edit');
    }

    public function changePasswordAction()
    {
        $this->get('cms.breadcrumbs')->add('change-password', 'Смена пароля');

        return $this->forward('FOSUserBundle:ChangePassword:changePassword');
    }

    public function resettingRequestAction()
    {
        if (!$this->node->getParam('allow_password_resetting')) {
            return new RedirectResponse($this->generateUrl('fos_user_profile_show'));
        }

        $this->get('cms.breadcrumbs')->add('resetting', 'Восстановление пароля');

        return $this->forward('FOSUserBundle:Resetting:request');
    }

    public function registerAction()
    {
        if (!$this->node->getParam('allow_registration')) {
            return new RedirectResponse($this->generateUrl('fos_user_profile_show'));
        }

        $this->get('cms.breadcrumbs')->add('register', 'Регистрация');

        if ($this->isGranted('IS_AUTHENTICATED_FULLY') or $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new RedirectResponse($this->generateUrl('fos_user_profile_show'));
        }

        return $this->forward('FOSUserBundle:Registration:register');
    }

    public function registerCheckEmailAction()
    {
        if ($this->get('session')->has('fos_user_send_confirmation_email/email')) {
            return $this->forward('FOSUserBundle:Registration:checkEmail');
        }

        return new RedirectResponse($this->generateUrl('fos_user_profile_show'));
    }
}
