<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Monolith\CMSBundle\Entity\UserGroup;
use Monolith\CMSBundle\Manager\ContextManager;
use Monolith\CMSBundle\Manager\ModuleManager;
use Monolith\CMSBundle\Manager\ThemeManager;
use Smart\CoreBundle\Form\DataTransformer\HtmlTransformer;
use Monolith\CMSBundle\Entity\Node;
use Monolith\CMSBundle\Entity\Region;
use Monolith\CMSBundle\Form\Tree\FolderTreeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeFormType extends AbstractType
{
    /** @var ModuleManager */
    protected $moduleManager;

    /** @var ThemeManager */
    protected $themeManager;

    /** @var ContextManager */
    protected $context;

    /**
     * NodeFormType constructor.
     *
     * @param ModuleManager $moduleManager
     */
    public function __construct(ContextManager $context, ModuleManager $moduleManager, ThemeManager $themeManager)
    {
        $this->context       = $context;
        $this->moduleManager = $moduleManager;
        $this->themeManager  = $themeManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $moduleThemes = [];
        foreach ($this->themeManager->getModuleThemes($options['data']->getModule()) as $theme) {
            $moduleThemes[$theme] = $theme;
        }

        $builder
            ->add('module', ChoiceType::class, [
                'choices' => $this->moduleManager->allNodeModulesForForm(),
                'data' => 'TexterModuleBundle', // @todo !!! настройку модуля по умолчанию.
                'choice_translation_domain' => false,
            ])
            ->add('controller', ChoiceType::class, [
                //'attr' => ['readonly' => true],
                'choices' => $this->moduleManager->allNodeModulesControllersForForm(),
                'choice_translation_domain' => false,
            ])
            ->add('folder', FolderTreeType::class)
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'query_builder' => function (EntityRepository $er) {
                    $site = $this->context->getSiteId();

                    return $er->createQueryBuilder('b')->where('b.site = :site')->orderBy('b.position', 'ASC')->setParameter('site', $site);
                },
                'required' => true,
            ])
            ->add('controls_in_toolbar', ChoiceType::class, [
                'choices' => [
                    'No' => Node::TOOLBAR_NO,
                    'Only in self folder' => Node::TOOLBAR_ONLY_IN_SELF_FOLDER,
                    //Node::TOOLBAR_ALWAYS => 'Всегда', // @todo
                ],
            ])
            ->add('template', ChoiceType::class, [
                'choices'  => $moduleThemes,
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('description')
            ->add('position')
            ->add('priority')
            ->add($builder->create('code_before')->addViewTransformer(new HtmlTransformer(false)))
            ->add($builder->create('code_after')->addViewTransformer(new HtmlTransformer(false)))
            ->add('is_active', null, ['required' => false])
            ->add('is_cached', null, ['required' => false])
            ->add('is_use_eip', null, ['required' => false])
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
        ;

        if (empty($moduleThemes)) {
            $builder->remove('template');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Node::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'monolith_cms_node';
    }
}
