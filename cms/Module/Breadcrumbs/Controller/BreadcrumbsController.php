<?php

declare(strict_types=1);

namespace Monolith\Module\Breadcrumbs\Controller;

use Monolith\CMSBundle\Controller\AbstractNodeController;
use Monolith\CMSBundle\Tools\Breadcrumbs;
use Symfony\Component\HttpFoundation\Response;

class BreadcrumbsController extends AbstractNodeController
{
    /** @var string */
    public $delimiter = '&raquo;';

    /** @var bool   Скрыть "хлебные крошки", если выбрана корневая папка. */
    public $hide_if_only_home = true;

    /** @var string */
    public $css_class = 'nav-breadcrumbs';

    /**
     * @param Breadcrumbs $breadcrumbs
     *
     * @return Response
     */
    public function index(Breadcrumbs $breadcrumbs): Response
    {
        return $this->render('@BreadcrumbsModule/breadcrumbs.html.php', [
            'css_class' => $this->css_class,
            'delimiter' => $this->delimiter,
            'items'     => $breadcrumbs,
            'hide_if_only_home' => $this->hide_if_only_home,
        ]);
    }
}
