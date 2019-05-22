<?php

namespace Monolith\CMSBundle\Module;

use Smart\CoreBundle\AppBundle\Bundle;
//use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class ModuleBundle extends Bundle implements ModuleBundleInterface
{
    use ModuleBundleTrait;
}
