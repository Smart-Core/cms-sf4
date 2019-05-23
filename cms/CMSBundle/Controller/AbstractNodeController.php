<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractNodeController extends AbstractController
{
    use AbstractNodeControllerTrait;
}
