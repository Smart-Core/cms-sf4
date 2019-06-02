<?php

declare(strict_types=1);

namespace Monolith\Module\Entity;

use Knp\Menu\MenuItem;
use Monolith\CMSBundle\Module\ModuleBundle;

class EntityModuleBundle extends ModuleBundle
{
    protected $adminMenuBeforeCode = '<i class="fa fa-object-ungroup"></i>';

    /**
     * @param MenuItem $menu
     * @param array $extras
     *
     * @return MenuItem
     */
    public function buildAdminMenu(MenuItem $menu, array $extras = ['beforeCode' => '<i class="fa fa-angle-right"></i>']): MenuItem
    {
        if ($this->hasAdmin()) {
            if (!isset($extras['beforeCode'])) {
                $extras['beforeCode'] = $this->adminMenuBeforeCode;
            }

            $menu->addChild('Entities', [
                'uri' => $this->container->get('router')->generate('easyadmin'),
            ])->setExtras($extras);

            /*
            $em = $this->container->get('doctrine.orm.entity_manager');

            $configurations = $em->getRepository(UnicatConfiguration::class)->findAll();

            if (empty($configurations)) {
                $extras = [
                    'beforeCode' => '<i class="fa fa-cubes"></i>',
                    'translation_domain' => false,
                ];

                $menu->addChild($this->getShortName(), [
                    'uri' => $this->container->get('router')->generate('cms_admin_index').$this->getShortName().'/'
                ])
                    ->setExtras($extras)
                ;
            } else {
                foreach ($configurations as $uc) {
                    $beforeCode = '<i class="fa fa-angle-right"></i>';

                    if (!empty($uc->getIcon())) {
                        $beforeCode = '<i class="fa fa-'.$uc->getIcon().'"></i>';
                    }

                    $menu->addChild($uc->getTitle(), [
                        'route' => 'unicat_admin.configuration',
                        'routeParameters' => ['configuration' => $uc->getName()],
                    ])->setExtras([
                        'beforeCode' => $beforeCode,
                        'translation_domain' => false,
                    ]);
                }
            }
            */
        }

        return $menu;
    }
}
