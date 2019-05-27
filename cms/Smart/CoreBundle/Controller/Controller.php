<?php

namespace Smart\CoreBundle\Controller;

use Smart\CoreBundle\Flash\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

/**
 * Inspired by KnpRadBundle
 *
 * deprecated
 */
class Controller extends BaseController
{
    // Doctrine

    protected function persist($object, $flush = false)
    {
        $this->get('doctrine.orm.entity_manager')->persist($object);

        if ($flush) {
            $this->flush($object);
        }
    }

    protected function flush($object = null)
    {
        $this->get('doctrine.orm.entity_manager')->flush($object);
    }

    protected function remove($object, $flush = false)
    {
        $this->get('doctrine.orm.entity_manager')->remove($object);

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * Gets the repository for an entity class.
     *
     * @param string $entityName The name of the entity.
     *
     * @return \Doctrine\ORM\EntityRepository The repository class.
     */
    protected function getRepository($entityName)
    {
        return $this->get('doctrine.orm.entity_manager')->getRepository($entityName);
    }

    // Sessions and flashes

    protected function getSession()
    {
        return $this->get('session');
    }

    /**
     * @param       $type
     * @param null  $message
     * @param array $parameters
     * @param null  $pluralization
     *
     * @return mixed
     *
     * @deprecated
     */
    protected function addFlashAdvanced($type, $message = null, array $parameters = [], $pluralization = null)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('You can not use the addFlash method if sessions are disabled.');
        }

        $message = $message ?: sprintf('%s.%s', $this->get('request_stack')->getMasterRequest()->attributes->get('_route'), $type);

        return $this->getSession()->getFlashBag()->add($type, new Message($message, $parameters, $pluralization));
    }

    protected function getFlashBag()
    {
        return $this->getSession()->getFlashBag();
    }

    protected function getFlash($type, $default = null)
    {
        if ($this->getSession()->getFlashBag()->has($type)) {
            return $this->getSession()->getFlashBag()->get($type);
        }

        return $default;
    }

    /**
     * Gets a container configuration parameter by its name.
     *
     * @return mixed
     */
    protected function getParameter(string $name)
    {
        return $this->container->getParameter($name);
    }
}
