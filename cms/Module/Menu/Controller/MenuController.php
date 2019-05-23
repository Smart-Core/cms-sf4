<?php

namespace Monolith\Module\Menu\Controller;

use Monolith\CMSBundle\Annotation\NodePropertiesForm;
use Monolith\CMSBundle\Controller\AbstractNodeController;
use Monolith\CMSBundle\Entity\Node;
use Monolith\CMSBundle\Module\CacheTrait;
use Monolith\Module\Menu\Entity\Menu;
use Monolith\Module\Menu\Form\Type\NodePropertiesFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractNodeController
{
    use CacheTrait;

    public $menu_id = 0;
    public $css_class = 'nav-menu';
    public $current_class = 'active';
    public $depth = 0;
    public $selected_inheritance = false;

    protected $nodePropertiesFormTypeClass = NodePropertiesFormType::class;

    /**
     * @param Request $request
     * @param Node    $node
     *
     * @return Response
     *
     * NodePropertiesForm("NodePropertiesFormType")
     */
    public function index(Request $request, Node $node): Response
    {
        $cmsSecurity = $this->container->get('cms.security');

        if ($cmsSecurity->isSuperAdmin()) {
            $userGroups = 'ROLE_SUPER_ADMIN';
        } else {
            $userGroups = serialize($cmsSecurity->getUserGroups());
        }

        $current_folder_path = $this->get('cms.context')->getCurrentFolderPath();

        $cache_key = md5('monolith_module.menu'.$current_folder_path.',node_id='.$node->getId().',groups='.$userGroups);

        $menu = $node->isCached() ? $this->getCacheService()->get($cache_key) : null;

        if (null === $menu) {
            // Хак для Menu\RequestVoter
            $request->attributes->set('__selected_inheritance', $this->selected_inheritance);
            $request->attributes->set('__current_folder_path', $current_folder_path);

            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->get('doctrine.orm.entity_manager');

            $menu = $this->renderView('@MenuModule/menu.html.twig', [
                'css_class'     => $this->css_class,
                'current_class' => $this->current_class,
                'depth'         => $this->depth,
                'menu'          => $em->find(Menu::class, $this->menu_id),
            ]);

            //$menu = $this->get('html.tidy')->prettifyFragment($menu);

            if ($node->isCached()) {
                $this->getCacheService()->set($cache_key, $menu, ['monolith_module.menu', 'folder', 'node_'.$node->getId()]);
            }

            $request->attributes->remove('__selected_inheritance');
            $request->attributes->remove('__current_folder_path');
        }

        $node->addFrontControl('edit')
            ->setTitle('Редактировать меню')
            ->setUri($this->generateUrl('monolith_module.menu.admin_menu', [
                'id' => $this->menu_id,
            ]));

        return new Response($menu);
    }
}
