<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller\Admin;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Monolith\CMSBundle\Entity\Permission;
use Monolith\CMSBundle\Entity\UserGroup;
use Monolith\CMSBundle\Form\Type\UserGroupFormType;
use Smart\CoreBundle\Controller\Controller;
use Monolith\CMSBundle\Entity\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Security("is_granted('ROLE_ADMIN_USER') or is_granted('ROLE_SUPER_ADMIN')")
 *
 * @Route("/user")
 */
class AdminUserController extends Controller
{
    /**
     * Список всех пользователей.
     *
     * @return Response
     *
     * @Route("/", name="cms_admin.user")
     *
     * @todo постраничность
     */
    public function indexAction(): Response
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        // @todo заменить на %user_class%

        return $this->render('@CMS/Admin/User/index.html.twig', [
            'users' => $em->getRepository('App:User')->findBy([], ['id' => 'DESC']),
        ]);
    }

    /**
     * На основе \FOS\UserBundle\Controller\RegistrationController::registerAction.
     *
     * @param Request $request
     *
     * @return null|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/create/", name="cms_admin.user_create")
     */
    public function createAction(Request $request): Response
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->get('cms.form.registration.admin.factory')->createForm();
        $form->setData($user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->get('router')->generate('cms_admin.user');
                    $response = new RedirectResponse($url);
                }

                $this->addFlash('success', 'Новый пользователь <b>'.$user->getUsername().'</b> создан.');

                return $response;
            }
        }

        return $this->render('@CMS/Admin/User/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * На основе \FOS\UserBundle\Controller\ProfileController::editAction.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id<\d+>}/", name="cms_admin.user_edit")
     */
    public function editAction(Request $request, int $id): Response
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserBy(['id' => $id]);
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('cms_admin.user'));
        }

        $form = $this->get('cms.form.profile.admin.factory')->createForm();
        $form->setData($user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $event = new GetResponseUserEvent($user, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

                $userManager->updateUser($user);

                $event = new GetResponseUserEvent($user, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, $event);

                $this->addFlash('success', 'Данные пользовалеля <b>'.$user->getUsername().'</b> обновлены.');

                return $this->redirect($this->generateUrl('cms_admin.user'));
            }
        }

        return $this->render('@CMS/Admin/User/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN_USER_GROUPS') or is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/groups/", name="cms_admin.user_groups")
     *
     * @todo вынести в контроллер AdminSecurity
     */
    public function groupsAction()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $groups = $em->getRepository(UserGroup::class)->findBy([], ['position' => 'ASC']);

        return $this->render('@CMS/Admin/User/groups.html.twig', [
            'groups' => $groups,
        ]);
    }

    /**
     * @param Request   $request
     * @param UserGroup $userGroup
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN_USER_GROUPS') or is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/group/{id<\d+>}/", name="cms_admin.user_group_edit")
     *
     * @todo вынести в контроллер AdminSecurity
     */
    public function groupEditAction(Request $request, UserGroup $userGroup): Response
    {
        $form = $this->createForm(UserGroupFormType::class, $userGroup);
        $form->add('update', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
        $form->add('cancel', SubmitType::class, ['attr' => ['class' => 'btn-default', 'formnovalidate' => 'formnovalidate']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('cms_admin.user_groups');
            }

            if ($form->get('update')->isClicked() and $form->isValid()) {
                $roles = [];
                $cnt = 0;
                foreach ($userGroup->getPermissions() as $permission) {
                    foreach ($permission->getRoles() as $role) {
                        $roles[$role] = $cnt++;
                    }
                }

                $userGroup->setRoles(array_flip($roles));

                $this->persist($userGroup, true);

                $this->addFlash('success', 'User group updated successfully.');

                return $this->redirectToRoute('cms_admin.user_groups');
            }
        }

        return $this->render('@CMS/Admin/User/group_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN_USER_GROUPS') or is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/group/create/", name="cms_admin.user_group_create")
     *
     * @todo вынести в контроллер AdminSecurity
     */
    public function groupCreateAction(Request $request): Response
    {
        $userGroup = new UserGroup('');

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        foreach ($em->getRepository(Permission::class)->findBy(['default_value' => 1]) as $permission) {
            $userGroup->addPermission($permission);
        }

        $form = $this->createForm(UserGroupFormType::class, $userGroup);
        $form->add('create', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
        $form->add('cancel', SubmitType::class, ['attr' => ['class' => 'btn-default', 'formnovalidate' => 'formnovalidate']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('cms_admin.user_groups');
            }

            if ($form->get('create')->isClicked() and $form->isValid()) {
                $this->persist($userGroup, true);

                $this->get('cms.security')->addDefaultPermissionsGroupForAllFolders($userGroup);
                $this->get('cms.security')->addDefaultPermissionsGroupForAllNodes($userGroup);
                $this->get('cms.security')->addDefaultPermissionsGroupForAllRegions($userGroup);
                $this->get('cms.cache')->invalidateTags(['node', 'folder', 'region']);

                $this->addFlash('success', 'User group created successfully.');

                return $this->redirectToRoute('cms_admin.user_groups');
            }
        }

        return $this->render('@CMS/Admin/User/group_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
