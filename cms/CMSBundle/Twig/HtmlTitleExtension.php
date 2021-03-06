<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Twig;

use Monolith\CMSBundle\Tools\Breadcrumbs;
use SmartCore\Bundle\SettingsBundle\Manager\SettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HtmlTitleExtension extends AbstractExtension
{
    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Breadcrumbs $breadcrumbs
     * @param SettingsManager $engineManager
     */
    public function __construct(Breadcrumbs $breadcrumbs, SettingsManager $engineManager)
    {
        $this->breadcrumbs  = $breadcrumbs;

        $this->options = [
            'delimiter'       => $engineManager->get('cms:html_title_delimiter'),
            'site_short_name' => $engineManager->get('cms:site_short_name'),
            'site_full_name'  => $engineManager->get('cms:site_full_name'),
        ];
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('cms_html_title', [$this, 'renderTitle']),
        ];
    }

    /**
     * @param  array $options
     *
     * @return string
     */
    public function renderTitle(array $options = []): string
    {
        $options = $options + $this->options;

        $response = (count($this->breadcrumbs) <= 1)
            ? $options['site_full_name']
            : $options['site_short_name'];

        foreach ($this->breadcrumbs->all() as $key => $value) {
            if ($key == 0) {
                continue;
            }

            $response = $value['title'].' '.$options['delimiter'].' '.$response;
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'monolith_cms_html_title_twig_extension';
    }
}
