<?php

declare(strict_types=1);

namespace Monolith\Module\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Monolith\CMSBundle\Controller\AbstractModuleNodeController;
use Monolith\CMSBundle\Manager\ContextManager;
use Monolith\CMSBundle\Module\CacheTrait;
use Monolith\Module\Menu\Entity\Menu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractModuleNodeController
{
    use CacheTrait;

    public $menu_id = 0;
    public $css_class = 'nav-menu';
    public $current_class = 'active';
    public $depth = 0;
    public $selected_inheritance = false;

    /**
     * @param Request                $request
     * @param ContextManager         $cmsContext
     * @param EntityManagerInterface $em
     *
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request, ContextManager $cmsContext, EntityManagerInterface $em): Response
    {
        $cmsSecurity = $this->container->get('cms.security');

        if ($cmsSecurity->isSuperAdmin()) {
            $userGroups = 'ROLE_SUPER_ADMIN';
        } else {
            $userGroups = serialize($cmsSecurity->getUserGroups());
        }

        $cache_key = md5('monolith_module.menu'.$cmsContext->getCurrentFolderPath().',node_id='.$this->node->getId().',groups='.$userGroups);

        $menu = $this->node->isCached() ? $this->getCacheService()->get($cache_key) : null;

        if (null === $menu) {
            // Хак для Menu\RequestVoter
            $request->attributes->set('__selected_inheritance', $this->selected_inheritance);
            $request->attributes->set('__cms_current_folder_path', $cmsContext->getCurrentFolderPath());

            $menu = $this->renderView('@MenuModule/menu.html.twig', [
                'css_class'     => $this->css_class,
                'current_class' => $this->current_class,
                'depth'         => $this->depth,
                'menu'          => $em->find(Menu::class, $this->menu_id),
            ]);

            //$menu = $this->get('html.tidy')->prettifyFragment($menu);

            if ($this->node->isCached()) {
                $this->getCacheService()->set($cache_key, $menu, ['monolith_module.menu', 'folder', 'node_'.$this->node->getId()]);
            }

            $request->attributes->remove('__selected_inheritance');
            $request->attributes->remove('__cms_current_folder_path');
        }

        $this->node->addFrontControl('edit')
            ->setTitle('Редактировать меню')
            ->setUri($this->generateUrl('monolith_module.menu.admin_menu', [
                'id' => $this->menu_id,
            ]));

        return new Response($menu);
    }
}
