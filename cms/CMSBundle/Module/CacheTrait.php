<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Module;

use Monolith\CMSBundle\Cache\CmsCacheProvider;

trait CacheTrait
{
    /**
     * @return CmsCacheProvider
     */
    protected function getCacheService(): CmsCacheProvider
    {
        return $this->get('cms.cache');
    }
}
