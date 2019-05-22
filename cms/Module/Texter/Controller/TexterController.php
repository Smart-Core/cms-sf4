<?php

declare(strict_types=1);

namespace Monolith\Module\Texter\Controller;

use Monolith\CMSBundle\Controller\AbstractNodeController;
use Monolith\CMSBundle\Entity\Node;
use Symfony\Component\HttpFoundation\Response;

class TexterController extends AbstractNodeController
{
    /** @var int */
    public $text_item_id = 0;

    /** @var bool */
    public $editor = true;

    public function indexAction(Node $node): Response
    {
        if ($item = $this->get('monolith_module.texter')->get($this->text_item_id, $node->getId())) {
            $node->addFrontControl('edit') // @todo убрать в cms.context
                ->setTitle('Редактировать текст')
                ->setUri($this->generateUrl('monolith_module.texter.admin.edit', ['id' => $this->text_item_id]));

            return new Response($item->getText());
        }

        return new Response("Texter not found. Node: {$node->getId()}<br />\n");
    }
}
