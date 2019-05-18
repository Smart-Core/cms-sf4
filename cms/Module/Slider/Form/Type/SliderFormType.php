<?php

namespace Monolith\Module\Slider\Form\Type;

use Monolith\Module\Slider\Entity\Slider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SliderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['attr' => ['autofocus' => 'autofocus']])
            ->add('width')
            ->add('height')
            ->add('pause_time')
            ->add('web_path')
            ->add('mode', ChoiceType::class, [
                'choices' => [
                    'INSET'    => 'INSET',
                    'OUTBOUND' => 'OUTBOUND',
                ],
                'choice_translation_domain' => false,
            ])
            ->add('library', ChoiceType::class, [
                'choices' => [
                    'jcarousel' => 'jcarousel',
                    'nivoslider' => 'nivoslider',
                    'bootstrap3' => 'bootstrap3',
                    'custom' => 'custom',
                ],
                'choice_translation_domain' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Slider::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'monolith_module_slider';
    }
}
