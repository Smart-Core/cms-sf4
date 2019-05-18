<?php

namespace Monolith\Module\Slider;

use Monolith\CMSBundle\Module\ModuleBundle;

class SliderModuleBundle extends ModuleBundle
{
    protected $adminMenuBeforeCode = '<i class="fa fa-tv"></i>';

    /**
     * Получить виджеты для рабочего стола.
     *
     * @return array
     */
    public function getDashboard()
    {
        $data = [
            'title' => 'Слайдер',
            'items' => [],
        ];

        foreach ($this->container->get('monolith_module.slider')->allSliders() as $slider) {
            $data['items']['edit_slider_'.$slider->getId()] = [
                'title' => 'Редактировать слайдер: <b>'.$slider->getTitle().'</b>',
                'descr' => '',
                'url' => $this->container->get('router')->generate('monolith_module.slider.admin_slider', ['id' => $slider->getId()]),
            ];
        }

        return $data;
    }
}
