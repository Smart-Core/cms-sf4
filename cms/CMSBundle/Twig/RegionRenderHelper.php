<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Twig;

/**
 * @deprecated пересмотреть логику.
 *
 * @todo возможно лучше избавиться и просто получать в твиг функцию просто массив.
 */
class RegionRenderHelper // implements \Iterator
{
    public function __toString(): string
    {
        return $this->render();
    }

    public function render(): string
    {
        $output = '';

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        foreach ($this as $_dummy_nodeId => $response) {
            $output .= $response->getContent();
        }

        return $output;
    }

    public function count(): int
    {
        $cntNodes = 0;
        foreach ($this as $_dummy_nodeId => $response) {
            $cntNodes++;
        }

        return $cntNodes;
    }
}
