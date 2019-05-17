<?php

declare(strict_types=1);

namespace Monolith\ModuleTexter\Controller;

use Monolith\CMSBundle\Module\NodeTrait;
use Smart\CoreBundle\Controller\Controller;

/**
 * @deprecated
 */
abstract class AbstractController extends Controller
{
    use NodeTrait;

    /**
     * Для каждого экземпляра ноды хранится ИД текстовой записи.
     *
     * @var int
     */
    protected $text_item_id;

    /**
     * Какой редактор использовать.
     * Пока используется как флаг, где:
     *  0 - Codemirror
     *  1 - TinyMCE.
     *
     * @var int
     */
    protected $editor = 1;
}
