<?php

declare(strict_types=1);

namespace Monolith\Module\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Monolith\CMSBundle\Controller\AbstractAdminController;
use Monolith\CMSBundle\Module\CacheTrait;
use Monolith\Module\Menu\Entity\Menu;
//use Smart\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Monolith\Module\Menu\Entity\MenuItem;
use Monolith\Module\Menu\Form\Type\MenuFormType;
use Monolith\Module\Menu\Form\Type\ItemFormType;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractAdminController
{
    use CacheTrait;

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return RedirectResponse|Response
     *
     * @Route("/", name="monolith_module.menu.admin")
     */
    public function indexAction(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MenuFormType::class);

        $site = $this->get('cms.context')->getSite();

        if ($request->isMethod('POST') and $request->request->has('create')) {
            $form->handleRequest($request);
            $menu = $form->getData();
            $menu
                ->setSite($site)
                ->setUser($this->getUser())
            ;

            if ($form->isValid()) {
                $em->persist($menu);
                $em->flush($menu);

                $this->getCacheService()->invalidateTag('monolith_module.menu');

                $this->addFlash('success', 'Меню создано.');

                return $this->redirectToRoute('monolith_module.menu.admin_menu', ['id' => $menu->getId()]);
            }
        }

        return $this->render('@MenuModule/Admin/index.html.twig', [
            'menus' => $this->get('doctrine.orm.default_entity_manager')->getRepository(Menu::class)->findBy(['site' => $site]),
            'form'  => $form->createView(),
        ]);
    }

    /**
     * Редактирование группы меню.
     *
     * @param Request $request
     * @param int $menu_id
     *
     * @return RedirectResponse|Response
     *
     * @Route("/{id<\d+>}/", name="monolith_module.menu.admin_menu")
     */
    public function menuAction(Request $request, Menu $menu, EntityManagerInterface $em)
    {
        $form = $this->createForm(ItemFormType::class, new MenuItem($menu));

        if ($request->isMethod('POST')) {
            if ($request->request->has('create_item')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    /** @var MenuItem $item */
                    $item = $form->getData();
                    $item
                        ->setUser($this->getUser())
                        ->setMenu($menu)
                    ;
                    $em->persist($item);
                    $em->flush($item);

                    $this->getCacheService()->invalidateTag('monolith_module.menu');

                    $this->addFlash('success', 'Пункт меню создан.');

                    return $this->redirectToRoute('monolith_module.menu.admin_menu', ['id' => $menu->getId()]);
                }
            }
        }

        return $this->render('@MenuModule/Admin/menu.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Редактирование свойств группы меню.
     *
     * @param Request $request
     * @param Menu $menu
     *
     * @return RedirectResponse|Response
     *
     * @Route("/{id<\d+>}/edit", name="monolith_module.menu.admin_menu_edit")
     */
    public function menuEditAction(Request $request, Menu $menu, EntityManagerInterface $em)
    {
        $form = $this->createForm(MenuFormType::class, $menu);

        if ($request->isMethod('POST')) {
            if ($request->request->has('update')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em->persist($form->getData());
                    $em->flush($form->getData());

                    $this->getCacheService()->invalidateTag('monolith_module.menu');
                    $this->addFlash('success', 'Группа меню обновлена.');

                    return $this->redirectToRoute('monolith_module.menu.admin_menu', ['id' => $menu->getId()]);
                }
            } elseif ($request->request->has('delete')) {
                // @todo безопасное удаление, в частности отключение из нод и удаление всех связаных пунктов меню.
                $em->remove($form->getData());
                $em->flush($form->getData());

                $this->getCacheService()->invalidateTag('monolith_module.menu');
                $this->addFlash('success', 'Группа меню удалена.');

                return $this->redirectToRoute('monolith_module.menu.admin');
            }
        }

        return $this->render('@MenuModule/Admin/menu_edit.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Редактирование пункта меню.
     *
     * @param Request $request
     * @param MenuItem $item
     *
     * @return RedirectResponse|Response
     *
     * @Route("/item/{id<\d+>}/", name="monolith_module.menu.admin_item")
     */
    public function itemAction(Request $request, MenuItem $item, EntityManagerInterface $em)
    {
        $form = $this->createForm(ItemFormType::class, $item);

        if ($request->isMethod('POST')) {
            if ($request->request->has('update')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em->persist($form->getData());
                    $em->flush($form->getData());

                    $this->getCacheService()->invalidateTag('monolith_module.menu');
                    $this->addFlash('success', 'Пункт меню обновлён.');

                    return $this->redirectToRoute('monolith_module.menu.admin_menu', ['id' => $item->getMenu()->getId()]);
                }
            } elseif ($request->request->has('delete')) {
                // @todo безопасное удаление, в частности отключение из нод и удаление всех связаных пунктов меню.
                $em->remove($form->getData());
                $em->flush($form->getData());

                $this->getCacheService()->invalidateTag('monolith_module.menu');
                $this->addFlash('success', 'Пункт меню удалён.');

                return $this->redirectToRoute('monolith_module.menu.admin_menu', ['id' => $item->getMenu()->getId()]);
            }
        }

        return $this->render('@MenuModule/Admin/item.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }
}
