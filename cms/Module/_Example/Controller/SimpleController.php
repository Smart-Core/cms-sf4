<?php
/**
 * Любой класс унаследованный от AbstractModuleController
 */
declare(strict_types=1);

use Monolith\CMSBundle\Controller\AbstractModuleNodeController;
use Symfony\Component\HttpFoundation\Response;

class SimpleController extends AbstractModuleNodeController
{
    protected $delimiter;

    public function run(CmsBreadcrumbs $breadcrumbs): Response
    {
        return $this->render('@ExampleModule/breadcrumbs.html.twig', [
            'node_id'   => $this->node->getId(),
            'delimiter' => $this->delimiter,
            'items'     => $breadcrumbs,
        ]);
    }
}
