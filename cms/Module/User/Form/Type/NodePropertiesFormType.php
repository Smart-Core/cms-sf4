<?php

declare(strict_types=1);

namespace Monolith\Module\User\Form\Type;

use Monolith\CMSBundle\Module\AbstractNodePropertiesFormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class NodePropertiesFormType extends AbstractNodePropertiesFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('allow_registration', CheckboxType::class, ['required' => false])
            ->add('allow_password_resetting', CheckboxType::class, ['required' => false])
        ;
    }

    public function getBlockPrefix()
    {
        return 'monolith_module_user_node_properties';
    }
}
