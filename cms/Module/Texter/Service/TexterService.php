<?php

namespace Monolith\Module\Texter\Service;

use Doctrine\ORM\EntityManager;
use Monolith\CMSBundle\Cache\CmsCacheProvider;
use Monolith\Module\Texter\Entity\TextItem;

class TexterService
{
    /**
     * @var CmsCacheProvider
     */
    protected $cache;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * TexterService constructor.
     *
     * @param CmsCacheProvider  $cache
     * @param EntityManager $em
     */
    public function __construct(CmsCacheProvider $cache, EntityManager $em)
    {
        $this->cache = $cache;
        $this->em    = $em;
    }

    /**
     * @param int $item_id
     * @param int|null $node_id - укаывается для кеширования.
     *
     * @return TextItem
     */
    public function get($item_id, $node_id = null): ?TextItem
    {
        $cache_key = 'monolith_module.texter'.$item_id;

        if (null === $item = $this->cache->get($cache_key)) {
            $item = $this->em->find(TextItem::class, $item_id);

            if ($node_id) {
                $this->cache->set($cache_key, $item, ['monolith_module.texter', 'node_'.$node_id]);
            }
        }

        return $item;
    }
}
