<?php

declare(strict_types=1);

namespace Monolith\Module\Breadcrumbs\Controller;

use Monolith\CMSBundle\Annotation\NodePropertiesForm;
use Monolith\CMSBundle\Controller\AbstractNodeController;
use Monolith\CMSBundle\Entity\Node;
use Monolith\Module\Breadcrumbs\Form\Type\NodePropertiesFormType;
use Symfony\Component\HttpFoundation\Response;

class BreadcrumbsController extends AbstractNodeController
{
    /** @var string */
    public $delimiter = '&raquo;';

    /** @var bool   Скрыть "хлебные крошки", если выбрана корневая папка. */
    public $hide_if_only_home = true;

    /** @var string */
    public $css_class = 'nav-breadcrumbs';

    protected $nodePropertiesFormTypeClass = NodePropertiesFormType::class;

    /**
     * @param Node   $node
     * @param string $delimiter
     * @param bool   $hide_if_only_home      Скрыть "хлебные крошки", если выбрана корневая папка.
     * @param null   $css_class
     *
     * @return Response
     *
     * @NodePropertiesForm("NodePropertiesFormType") @todo remove
     */
    public function indexAction(Node $node): Response
    {
        return $this->render('@BreadcrumbsModule/breadcrumbs.html.php', [
            'css_class' => $this->css_class,
            'delimiter' => $this->delimiter,
            'items'     => $this->get('cms.breadcrumbs'),
            'hide_if_only_home' => $this->hide_if_only_home,
        ]);
    }
}
