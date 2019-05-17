<?php

namespace Smart\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class BooleanToStringTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return empty($value) ? false : true;
    }

    public function reverseTransform($value)
    {
        return empty($value) ? '0' : '1';
    }
}
