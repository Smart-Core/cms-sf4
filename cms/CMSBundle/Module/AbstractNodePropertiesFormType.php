<?php

namespace Monolith\CMSBundle\Module;

use Doctrine\ORM\EntityManager;
use Monolith\CMSBundle\Container;
use Monolith\CMSBundle\Manager\ContextManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractNodePropertiesFormType extends AbstractType
{
    /** @var EntityManager */
    protected $em;

    /** @var KernelInterface */
    protected $kernel;

    /** @var ContextManager */
    protected $context;

    /**
     * @param EntityManager   $em
     * @param KernelInterface $kernel
     */
    //public function __construct(EntityManager $em, KernelInterface $kernel)
    public function __construct()
    {
        $this->context  = Container::get('cms.context');
        $this->em       = Container::get('doctrine.orm.entity_manager');
        $this->kernel   = Container::get('kernel');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    /**
     * @param string $entityName
     *
     * @return array
     */
    protected function getChoicesByEntity($entityName, $only_for_site = false)
    {
        $criteria = [];

        if ($only_for_site) {
            $site  = $this->context->getSite();

            $criteria = ['site' => $site];
        }

        $choices = [];
        foreach ($this->em->getRepository($entityName)->findBy($criteria) as $choice) {
            $choices[(string) $choice] = $choice->getId();
        }

        return $choices;
    }

    /**
     * @param EntityManager $em
     *
     * @return $this
     */
    public function setEm($em): self
    {
        $this->em = $em;

        return $this;
    }

    /**
     * @param KernelInterface $kernel
     *
     * @return $this
     */
    public function setKernel($kernel): self
    {
        $this->kernel = $kernel;

        return $this;
    }

    /**
     * @param ContextManager $context
     *
     * @return $this
     */
    public function setContext($context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return string
     */
    public static function getTemplate(): string
    {
        return '@CMS/Admin/Structure/node_properties_form.html.twig';
    }
}
