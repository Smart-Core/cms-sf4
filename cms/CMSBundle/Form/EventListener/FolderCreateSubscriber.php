<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Form\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FolderCreateSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * FolderCreateSubscriber constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT => 'submit',
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
//        dump(__METHOD__);

        $data = $event->getData();
        $form = $event->getForm();

        /*
        $form->add('is_create', CheckboxType::class, [
            'required' => false,
            'mapped' => false,
        ]);
        */
    }

    public function postSetData(FormEvent $event): void
    {
//        dump(__METHOD__);

        $data = $event->getData();
        $form = $event->getForm();
    }

    public function preSubmit(FormEvent $event): void
    {
//        dump(__METHOD__);

        $data = $event->getData();
        $form = $event->getForm();
    }

    public function submit(FormEvent $event): void
    {
//        dump(__METHOD__);

        $data = $event->getData();
        $form = $event->getForm();
    }

    public function postSubmit(FormEvent $event): void
    {
//        dump(__METHOD__);

        $data = $event->getData();
        $form = $event->getForm();
    }
}
