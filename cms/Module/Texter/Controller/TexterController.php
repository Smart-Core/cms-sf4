<?php

declare(strict_types=1);

namespace Monolith\Module\Texter\Controller;

use Monolith\CMSBundle\Controller\AbstractModuleNodeController;
use Monolith\CMSBundle\Entity\Node;
use Symfony\Component\HttpFoundation\Response;

class TexterController extends AbstractModuleNodeController
{
    /** @var int */
    public $text_item_id = 0;

    /** @var bool */
    public $editor = true;

    /**
     * @param Node $node
     *
     * @return Response
     * @throws \Exception
     */
    public function index(): Response
    {
        if ($item = $this->get('monolith_module.texter')->get($this->text_item_id, $this->node->getId())) {
            $this->node->addFrontControl('edit') // @todo убрать в cms.context
                ->setTitle('Редактировать текст')
                ->setUri($this->generateUrl('monolith_module.texter.admin.edit', ['id' => $this->text_item_id]));

            return new Response($item->getText());
        }

        return new Response("Texter not found. Node: {$this->node->getId()}<br />\n");
    }

    /**
     * Действие при создании ноды.
     *
     * @param Node $node
     */
    public function createNode(Node $node): void
    {
        $item = $this->get('monolith_module.texter')->factory();
        $item->setUser($this->getUser());

        $this->get('monolith_module.texter')->create($item);

        $node->setParam('text_item_id', $item->getId());
    }

    /**
     * Действие при обновлении ноды.
     *
     * @param Node $node
     */
    public function updateNode(Node $node): void
    {
        $text_item_id = $node->getParam('text_item_id');

        if (empty($text_item_id)) {
            return;
        }

        $item = $this->get('monolith_module.texter')->get($text_item_id);

        if ($item) {
            $item->setEditor((int) $node->getParam('editor'));

            $this->get('monolith_module.texter')->update($item);
        }
    }
}
