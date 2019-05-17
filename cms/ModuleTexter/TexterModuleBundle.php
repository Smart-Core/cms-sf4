<?php

declare(strict_types=1);

namespace Monolith\ModuleTexter;

use Monolith\CMSBundle\Entity\Node;
use Monolith\CMSBundle\Module\ModuleBundle;
use Monolith\ModuleTexter\Entity\TextItem;

class TexterModuleBundle extends ModuleBundle
{
    protected $adminMenuBeforeCode = '<i class="fa fa-text-height"></i>';

    /**
     * Действие при создании ноды.
     *
     * @param Node $node
     */
    public function createNode(Node $node): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $item = new TextItem();
        $item->setUser($this->container->get('security.token_storage')->getToken()->getUser());

        $em->persist($item);
        $em->flush($item);

        $node->setParams([
            'text_item_id' => $item->getId(),
            'editor' => true,
        ]);
    }

    /**
     * Действие при обновлении ноды.
     *
     * @param Node $node
     */
    public function updateNode(Node $node): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $text_item_id = $node->getParam('text_item_id');

        if (empty($text_item_id)) {
            return;
        }

        $item = $em->find(TextItem::class, $text_item_id);

        if ($item) {
            $item->setEditor((int) $node->getParam('editor'));

            $em->persist($item);
            $em->flush($item);
        }
    }
}
