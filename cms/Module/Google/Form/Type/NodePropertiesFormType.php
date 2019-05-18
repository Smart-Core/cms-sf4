<?php

namespace Monolith\Module\Google\Form\Type;

use Monolith\CMSBundle\Module\AbstractNodePropertiesFormType;
use Symfony\Component\Form\FormBuilderInterface;

class NodePropertiesFormType extends AbstractNodePropertiesFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('some_value')
        ;
    }

    public function getBlockPrefix()
    {
        return 'google_module_node_properties';
    }
}
