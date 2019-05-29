<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller;

use Monolith\CMSBundle\Entity\Node;

interface ModuleNodeControllerInterface
{
    public function getNode(): Node;
    public function setNode(Node $node);
    public function createNode(Node $node);
    public function deleteNode(Node $node);
    public function updateNode(Node $node);

    public function getNodePropertiesFormTypeClass(): string;
    public function isDefaultNodePropertiesFormTypeClass(): bool;
}
