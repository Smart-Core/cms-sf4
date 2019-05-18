<?php

namespace Monolith\Module\Texter\Service;

use Doctrine\ORM\EntityManager;
use Monolith\CMSBundle\Cache\CacheWrapper;
use Monolith\Module\Texter\Entity\TextItem;

class TexterService
{
    /**
     * @var CacheWrapper
     */
    protected $cache;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * TexterService constructor.
     *
     * @param CacheWrapper  $cache
     * @param EntityManager $em
     */
    public function __construct(CacheWrapper $cache, EntityManager $em)
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
