<?php

declare(strict_types=1);

namespace Monolith\Module\Texter;

use Monolith\CMSBundle\Module\ModuleBundle;

class TexterModuleBundle extends ModuleBundle
{
    protected $adminMenuBeforeCode = '<i class="fa fa-text-height"></i>';

    protected $title = 'Текстовые блоки (Texter)';
}
