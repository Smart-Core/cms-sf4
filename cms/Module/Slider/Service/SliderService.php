<?php

namespace Monolith\Module\Slider\Service;

use Doctrine\ORM\EntityManager;
use Monolith\Module\Slider\Entity\Slide;
use Monolith\Module\Slider\Entity\Slider;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class SliderService
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $origitalDir;

    /**
     * @var string
     */
    protected $webDir;

    /**
     * Constructor.
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param  Slide $slide
     *
     * @return $this
     */
    public function save(Slide $slide)
    {
        $file = $slide->getFile();

        $filename = md5(uniqid(mt_rand(), true).$file->getFilename()).'.'.$file->guessExtension();
        $file->move($slide->getSlider()->getWebPath(), $filename);

        $slide
            ->setFileName($filename)
            ->setOriginalFileName($file->getClientOriginalName())
            ->setUser($this->container->get('security.token_storage')->getToken()->getUser())
        ;

        $this->em->persist($slide);
        $this->em->flush($slide);

        return $this;
    }

    /**
     * @return Slide[]
     */
    public function all()
    {
        return $this->em->getRepository('SliderModuleBundle:Slide')->findBy([], ['position' => 'ASC']);
    }

    /**
     * @return Slider[]
     */
    public function allSliders()
    {
        return $this->em->getRepository('SliderModuleBundle:Slider')->findBy([], ['title' => 'ASC']);
    }

    /**
     * @param Slider $slider
     *
     * @return $this
     */
    public function createSlider(Slider $slider)
    {
        $this->em->persist($slider);
        $this->em->flush($slider);

        return $this;
    }

    /**
     * @param Slider $slider
     *
     * @return $this
     */
    public function updateSlider(Slider $slider)
    {
        $this->em->persist($slider);
        $this->em->flush($slider);

        return $this;
    }

    /**
     * @param Slide $slide
     *
     * @return $this
     */
    public function updateSlide(Slide $slide)
    {
        $this->em->persist($slide);
        $this->em->flush($slide);

        return $this;
    }

    /**
     * @param Slide $slide
     *
     * @return $this
     */
    public function deleteSlide(Slide $slide)
    {
        unlink($slide->getSlider()->getWebPath().$slide->getFileName());

        $this->em->remove($slide);
        $this->em->flush($slide);

        return $this;
    }

    /**
     * @param  Slider $slider
     *
     * @return $this
     */
    public function deleteSlider(Slider $slider)
    {
        $this->em->remove($slider);
        $this->em->flush($slider);

        return $this;
    }

    /**
     * @param  int $id
     *
     * @return Slide
     */
    public function getSlide($id)
    {
        return $this->em->getRepository('SliderModuleBundle:Slide')->find($id);
    }

    /**
     * @param  int $id
     *
     * @return Slider
     */
    public function getSlider($id)
    {
        return $this->em->getRepository('SliderModuleBundle:Slider')->find($id);
    }

    /**
     * @throws \RuntimeException
     */
    public function initImagesDirectory()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        foreach ($em->getRepository(Slider::class)->findAll() as $slider) {
            $dir = $this->container->getParameter('kernel.project_dir').'/web/'.$slider->getWebPath();

            if (!is_dir($dir) and false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException(sprintf("Unable to create the %s directory (%s)\n", $slider->getWebPath(), $dir));
            }
        }
    }
}
