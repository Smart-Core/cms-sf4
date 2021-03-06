<?php

declare(strict_types=1);

namespace Monolith\Module\Menu\Entity;

use Doctrine\ORM\EntityRepository;

class MenuItemRepository extends EntityRepository
{
    /**
     * @param Menu          $menu
     * @param MenuItem|null $parent_item
     *
     * @return MenuItem[]
     */
    public function findByParent(Menu $menu, MenuItem $parent_item = null): array
    {
        return $this->findBy([
            'parent_item' => $parent_item,
            'menu'        => $menu,
        ], ['position' => 'ASC']);
    }
}
