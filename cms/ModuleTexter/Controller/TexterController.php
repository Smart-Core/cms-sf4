<?php

namespace Monolith\ModuleTexter\Controller;

use Monolith\CMSBundle\Entity\Node;
use Smart\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TexterController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction(Node $node, int $text_item_id, ?bool $editor = true)
    {
        if ($item = $this->get('monolith_module.texter')->get($text_item_id, $node->getId())) {
            $node->addFrontControl('edit')
                ->setTitle('Редактировать текст')
                ->setUri($this->generateUrl('monolith_module.texter.admin.edit', ['id' => $text_item_id]));

            return new Response($item->getText());
        }

        return new Response("Texter not found. Node: {$node->getId()}<br />\n");
    }
}
