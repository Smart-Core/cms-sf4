<?php

namespace Monolith\Module\Slider\Form\Type;

use Monolith\CMSBundle\Form\Tree\FolderTreeType;
use Monolith\Module\Slider\Entity\Slide;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlideEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('is_enabled')
            ->add('title', null, ['attr' => ['autofocus' => 'autofocus']])
            ->add('uri')
            ->add('folder', FolderTreeType::class, [
                'required' => false,
                'only_active' => true,
                'choice_translation_domain' => false,
            ])
            ->add('position')
            ->add('update', SubmitType::class, ['attr' => ['class' => 'btn btn-success']])
            ->add('delete', SubmitType::class, ['attr' => ['class' => 'btn btn-danger', 'onclick' => "return confirm('Вы уверены, что хотите удалить слайд?')"]])
            ->add('cancel', SubmitType::class, ['attr' => ['class' => 'btn-default', 'formnovalidate' => 'formnovalidate']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Slide::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'monolith_module_slider_item_edit';
    }
}
