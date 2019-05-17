<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Timezone;

use Monolith\CMSBundle\Container;
use Sonata\IntlBundle\Timezone\ChainTimezoneDetector;

class SonataChainTimezoneDetector extends ChainTimezoneDetector
{
    /**
     * @param string $defaultTimezone
     */
    public function __construct($defaultTimezone)
    {
        $timezone = Container::get('settings')->get('cms:timezone');

        parent::__construct($timezone);
    }
}
