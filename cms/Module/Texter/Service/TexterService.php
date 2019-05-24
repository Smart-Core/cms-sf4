<?php

declare(strict_types=1);

namespace Monolith\Module\Texter\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolith\CMSBundle\Cache\CmsCacheProvider;
use Monolith\Module\Texter\Entity\TextItem;

class TexterService
{
    /** @var CmsCacheProvider */
    protected $cache;

    /** @var EntityManagerInterface */
    protected $em;

    /**
     * TexterService constructor.
     *
     * @param CmsCacheProvider       $cache
     * @param EntityManagerInterface $em
     */
    public function __construct(CmsCacheProvider $cache, EntityManagerInterface $em)
    {
        $this->cache = $cache;
        $this->em    = $em;
    }

    /**
     * @param int $item_id
     * @param int|null $node_id - используется для кеширования.
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

    public function factory(): TextItem
    {
        return new TextItem();
    }

    public function create(TextItem $item): TextItem
    {
        $this->em->persist($item);
        $this->em->flush($item);

        return $item;
    }

    public function update($item): void
    {
        $this->em->persist($item);
        $this->em->flush($item);
    }
}
