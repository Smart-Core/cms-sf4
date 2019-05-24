<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller;

use Monolith\CMSBundle\Entity\Node;
use Monolith\CMSBundle\Form\Type\NodeDefaultPropertiesFormType;

trait AbstractNodeControllerTrait
{
    /** @var Node */
    protected $node;

    /** @var string */
    protected $nodePropertiesFormTypeClass = NodeDefaultPropertiesFormType::class;

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param mixed $node
     *
     * @return $this
     */
    public function setNode($node): self
    {
        $this->node = $node;

        return $this;
    }

    /**
     * @return string
     */
    public function getNodePropertiesFormTypeClass(): string
    {
        return $this->nodePropertiesFormTypeClass;
    }

    public function isDefaultNodePropertiesFormTypeClass(): bool
    {
        return $this->nodePropertiesFormTypeClass == NodeDefaultPropertiesFormType::class ? true : false;
    }

    /**
     * Действие при создании ноды.
     *
     * @param Node $node
     */
    public function createNode(Node $node)
    {
    }

    /**
     * Действие при удалении ноды.
     *
     * @param Node $node
     */
    public function deleteNode(Node $node)
    {
    }

    /**
     * Действие при обновлении ноды.
     *
     * @param Node $node
     */
    public function updateNode(Node $node)
    {
    }
}
