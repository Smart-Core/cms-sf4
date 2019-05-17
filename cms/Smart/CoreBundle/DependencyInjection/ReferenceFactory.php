<?php

namespace Smart\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;

class ReferenceFactory
{
    public function createReference($serviceId)
    {
        return new Reference($serviceId);
    }
}
