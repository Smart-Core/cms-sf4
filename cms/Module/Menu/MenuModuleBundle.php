<?php

declare(strict_types=1);

namespace Monolith\Module\Menu;

use Monolith\CMSBundle\Module\ModuleBundle;
use Monolith\Module\Menu\DependencyInjection\Compiler\FormPass;
use Monolith\Module\Menu\Entity\Menu;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MenuModuleBundle extends ModuleBundle
{
    protected $adminMenuBeforeCode = '<i class="fa fa-indent"></i>';

    /**
     * Получить виджеты для рабочего стола.
     *
     * @return array
     *
     * @deprecated - должно быть в контроллере
     */
    public function getDashboard(): array
    {
        $em     = $this->container->get('doctrine.orm.default_entity_manager');
        $r      = $this->container->get('router');
        $menus  = $em->getRepository(Menu::class)->findAll();

        $data = [
            'title' => 'Меню',
            'items' => [],
        ];

        foreach ($menus as $menu) {
            $data['items']['edit_menu_'.$menu->getId()] = [
                'title' => 'Редактировать меню: <b>'.$menu->getName().'</b>',
                'descr' => '',
                'url' => $r->generate('monolith_module.menu.admin_menu', ['id' => $menu->getId()]),
            ];
        }

        return $data;
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new FormPass());
    }
}
