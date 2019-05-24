<?php

declare(strict_types=1);

namespace Monolith\Module\Breadcrumbs\Form\Type;

use Smart\CoreBundle\Form\DataTransformer\HtmlTransformer;
use Monolith\CMSBundle\Module\AbstractNodePropertiesFormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NodePropertiesFormType extends AbstractNodePropertiesFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder->create('delimiter', TextType::class, [
                'attr' => ['autofocus' => 'autofocus'],
                ])->addViewTransformer(new HtmlTransformer(false)))
            ->add('hide_if_only_home', CheckboxType::class, ['required' => false])  // Скрыть, если выбрана корневая папка
            ->add('css_class', TextType::class, ['required' => false])              // CSS class div блока
        ;
    }

    public function getBlockPrefix()
    {
        return 'monolith_module_breadcrumbs_node_properties';
    }
}
