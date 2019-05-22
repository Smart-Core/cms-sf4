<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbstractNodeController extends AbstractController
{
    protected $node;

    protected $nodePropertiesFormClass; // \Monolith\CMSBundle\Form\Type\NodeDefaultPropertiesFormType::class

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
     * @return mixed
     */
    public function getNodePropertiesFormClass(): ?string
    {
        return $this->nodePropertiesFormClass;
    }
}
