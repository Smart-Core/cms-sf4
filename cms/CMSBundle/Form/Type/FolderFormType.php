<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Monolith\CMSBundle\Entity\Folder;
use Monolith\CMSBundle\Entity\UserGroup;
use Monolith\CMSBundle\Form\EventListener\FolderCreateSubscriber;
use Monolith\CMSBundle\Form\Tree\FolderTreeType;
use Monolith\CMSBundle\Manager\NodeManager;
use SmartCore\Bundle\SeoBundle\Form\Type\MetaFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FolderFormType extends AbstractType
{
    use ContainerAwareTrait;

    /** @var NodeManager */
    protected $cmsNode;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var KernelInterface */
    protected $kernel;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->eventDispatcher = $this->container->get('event_dispatcher');

        $templates = ['' => ''];

        $currentThemeDir = $this->container->get('kernel')->getBundle('CMSBundle')->getThemeDir().'/templates';

        if (file_exists($currentThemeDir)) {
            $finder = new Finder();
            $finder->files()->sortByName()->depth('== 0')->name('*.html.twig')->in($currentThemeDir);

            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            foreach ($finder as $file) {
                $name = str_replace('.html.twig', '', $file->getFilename());
                $templates[$name] = $name;
            }
        }

        $routedNodes = ['' => ''];
        foreach ($this->container->get('cms.node')->findInFolder($options['data']) as $node) {
            if (!$this->container->has('cms.router_module.'.$node->getController())) { // @todo перенести проверку наличия роутов у ноды в NodeManager.
                continue;
            }

            $nodeTitle = $node->getModuleShortName().' (node: '.$node->getId().')';

            if ($node->getDescription()) {
                $nodeTitle .= ' ('.$node->getDescription().')';
            }

            $routedNodes[$nodeTitle] = $node->getId();
        }

//        $event = new FolderCreateFormEvent($builder);

//        $this->eventDispatcher->dispatch('cms.foder_create_form.before', $event);

        $builder
            ->add('title', null, ['attr' => ['autofocus' => 'autofocus']])
            ->add('uri_part')
            ->add('description')
            ->add('parent_folder', FolderTreeType::class)
            ->add('router_node_id', ChoiceType::class, [
                'choices'  => $routedNodes,
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('position')
            ->add('is_active', null, ['required' => false])
            ->add('is_file',   null, ['required' => false])
            ->add('template_inheritable', ChoiceType::class, [
                'choices'  => $templates,
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('template_self', ChoiceType::class, [
                'choices'  => $templates,
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('meta', MetaFormType::class, ['label' => 'Meta tags'])
            ->add('groups_granted_read', EntityType::class, [
                'class' => UserGroup::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.position', 'ASC')
                        ->addOrderBy('e.title', 'ASC')
                        ;
                },
                'required'        => false,
                'expanded'        => true,
                'multiple'        => true,
                'choice_translation_domain' => false,
            ])
            ->add('groups_granted_write', EntityType::class, [
                'class' => UserGroup::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.position', 'ASC')
                        ->addOrderBy('e.title', 'ASC')
                        ;
                },
                'required'        => false,
                'expanded'        => true,
                'multiple'        => true,
                'choice_translation_domain' => false,
            ])

            //->add('lockout_nodes', 'text')
        ;

        if (count($templates) == 1) {
            $builder->remove('template_self');
            $builder->remove('template_inheritable');
        }

        if (count($routedNodes) == 1) {
            $builder->remove('router_node_id');
        }

        $folder = $options['data'];

        if ($folder->getId() === null) {
            $builder->addEventSubscriber(new FolderCreateSubscriber($this->container));
        }

        //$this->eventDispatcher->dispatch('cms.foder_create_form.after', $event);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Folder::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'monolith_cms_folder';
    }
}
