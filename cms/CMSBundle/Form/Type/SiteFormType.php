<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Monolith\CMSBundle\Container;
use Monolith\CMSBundle\Entity\Domain;
use Monolith\CMSBundle\Entity\Language;
use Monolith\CMSBundle\Entity\Site;
use Monolith\CMSBundle\Manager\ThemeManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteFormType extends AbstractType
{
    /** @var ThemeManager */
    protected $themeManager;

    /**
     * SiteFormType constructor.
     *
     * @param ThemeManager $themeManager
     */
    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager  = $themeManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $themes = [];
        foreach ($this->themeManager->all() as $item) {
            $themes[$item['title'].' ('.$item['dirname'].')'] = $item['dirname'];
        }

        $builder
            ->add('is_enabled')
            ->add('name', null, [
                'attr' => [
                    'autofocus' => 'autofocus',
                    'placeholder' => 'New site'
                ]
            ])
            ->add('domain', EntityType::class, [
                'class'         => Domain::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')->where('e.parent IS NULL');
                },
                'required' => false,
            ])
            ->add('language', EntityType::class, [
                'class'         => Language::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')->where('e.is_enabled = true')->orderBy('e.position', 'ASC');
                },
            ])
            ->add('web_root', null, [
                'attr' => [
                    'placeholder' => 'web/',
                ],
            ])
            ->add('theme', ChoiceType::class, [
                'choices'  => $themes,
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('position')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Site::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'monolith_cms_site';
    }
}
