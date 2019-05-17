<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Module;

use Monolith\CMSBundle\Cache\CacheWrapper;

trait CacheTrait
{
    /**
     * @return CacheWrapper
     */
    protected function getCacheService(): CacheWrapper
    {
        return $this->get('cms.cache');
    }
}
