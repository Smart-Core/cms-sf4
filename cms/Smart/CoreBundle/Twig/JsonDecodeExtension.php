<?php

namespace Smart\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;

class JsonDecodeExtension extends AbstractExtension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'twig.json_decode';
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('json_decode', [$this, 'jsonDecode']),
        );
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('json_decode', [$this, 'jsonDecode']),
        ];
    }

    /**
     * @param string $string
     * @param bool   $assoc
     * @param int    $depth
     * @param int    $options
     *
     * @return mixed
     */
    public function jsonDecode($string, $assoc = false, $depth = 512, $options = 0)
    {
        return json_decode($string, $assoc, $depth, $options);
    }
}
