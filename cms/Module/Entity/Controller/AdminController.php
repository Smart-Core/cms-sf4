<?php

declare(strict_types=1);

namespace Monolith\Module\Entity\Controller;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminControllerTrait;
use EasyCorp\Bundle\EasyAdminBundle\Search\Autocomplete;
use EasyCorp\Bundle\EasyAdminBundle\Search\Paginator;
use EasyCorp\Bundle\EasyAdminBundle\Search\QueryBuilder;
use Monolith\CMSBundle\Controller\AbstractModuleAdminController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractModuleAdminController
{
    use AdminControllerTrait;

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @Route("/", name="easyadmin")
     */
    public function indexAction(Request $request): Response
    {
        $entities = $this->container->get('easyadmin.config.manager')->getBackendConfig()['entities'];

        return $this->render('@EntityModule/Admin/index.html.twig', ['entities' => $entities]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @Route("/{entity}/", name="monolith_module.entity.admin.list")
     */
    public function listEntityAction(Request $request, string $entity): Response
    {
        $request->query->set('entity', $entity);
        $request->query->set('action', 'list');

        $this->initialize($request);

        dump($this->config);

        return $this->listAction();
    }

    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
                'easyadmin.autocomplete' => Autocomplete::class,
                'easyadmin.config.manager' => ConfigManager::class,
                'easyadmin.paginator' => Paginator::class,
                'easyadmin.query_builder' => QueryBuilder::class,
                'easyadmin.property_accessor' => PropertyAccessorInterface::class,
                'event_dispatcher' => EventDispatcherInterface::class,
            ];
    }
}
