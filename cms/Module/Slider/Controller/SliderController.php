<?php

namespace Monolith\Module\Slider\Controller;

use Monolith\CMSBundle\Annotation\NodePropertiesForm;
use Monolith\CMSBundle\Entity\Node;
use Monolith\Module\Slider\Entity\Slider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SliderController extends Controller
{
    /**
     * @param  Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @NodePropertiesForm("NodePropertiesFormType")
     */
    public function indexAction(Request $request, Node $node, $slider_id)
    {
        if (null === $slider_id) {
            return new Response();
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $slider = $em->find(Slider::class, $slider_id);

        $node->addFrontControl('manage_slider')
            ->setTitle('Управление слайдами')
            ->setUri($this->generateUrl('monolith_module.slider.admin_slider', ['id' => $slider->getId()]));

        return $this->get('twig')->render('@SliderModule/'.$slider->getLibrary().'.html.twig', [
            'slider'  => $slider,
            'imgPath' => $request->getBasePath().'/'.$slider->getWebPath(),
        ]);
    }
}
