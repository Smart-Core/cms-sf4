<?php

namespace Smart\CoreBundle\AppBundle;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class TreeBuilderFactory
{
    public function createTreeBuilder()
    {
        return new TreeBuilder;
    }
}
