<?php

declare(strict_types=1);

namespace Monolith\Module\Gallery\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Monolith\CMSBundle\Controller\AbstractNodeController;
use Monolith\CMSBundle\Entity\Node;
use Monolith\CMSBundle\Tools\Breadcrumbs;
use Monolith\Module\Gallery\Entity\Album;
use Monolith\Module\Gallery\Entity\Gallery;
use Monolith\Module\Gallery\Entity\Photo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractNodeController
{
    public $gallery_id;

    /**
     * @param EntityManagerInterface $em
     *
     * @return Response
     *
     * @Route("/", name="monolith_module.gallery")
     */
    public function index(EntityManagerInterface $em): Response
    {
        switch ($em->find(Gallery::class, $this->gallery_id)->getOrderAlbumsBy()) {
            case 1:
                $albumsOrderBy = ['position' => 'ASC'];
                break;
            case 2:
                $albumsOrderBy = ['position' => 'DESC'];
                break;
            default:
                $albumsOrderBy = ['id' => 'DESC'];
        }

        $albums = $em->getRepository(Album::class)->findBy(['is_enabled' => true, 'gallery' => $this->gallery_id], $albumsOrderBy);

        $this->node->addFrontControl('manage_gallery')
            ->setTitle('Управление фотогалереей')
            ->setUri($this->generateUrl('monolith_module.gallery.admin_gallery', ['id' => $this->gallery_id]));

        return $this->render('@GalleryModule/index.html.twig', [
            'albums'  => $albums,
        ]);
    }

    /**
     * @param int                    $id
     * @param EntityManagerInterface $em
     * @param Breadcrumbs            $breadcrumbs
     *
     * @return Response
     * @throws \Exception
     *
     * @Route("/{id<\d+>}/", name="monolith_module.gallery.album")
     */
    public function album($id, EntityManagerInterface $em, Breadcrumbs $breadcrumbs): Response
    {
        $album = $em->getRepository(Album::class)->find($id);

        if (empty($album) or $this->gallery_id != $album->getGallery()->getId() or $album->isDisabled()) {
            throw $this->createNotFoundException();
        }

        $this->node->addFrontControl('manage_album')
            ->setTitle('Редактировать фотографии')
            ->setUri($this->generateUrl('monolith_module.gallery.admin_album', [
                'id' => $id,
                'gallery_id' => $this->gallery_id,
            ]));

        $breadcrumbs->add((string) $album->getId(), $album->getTitle());

        $photos = $em->getRepository(Photo::class)->findBy(['album' => $album], ['id' => 'DESC']);

        return $this->render('@GalleryModule/photos.html.twig', [
            'photos'  => $photos,
        ]);
    }

    /**
     * @param Node $node
     * @param int  $count
     */
    protected function ___latestWidgetAction(Node $node, int $count = 5): Response
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $photos = $em->getRepository(Photo::class)->findBy([], ['id' => 'DESC'], $count);

        $node->addFrontControl('manage_album')
            ->setTitle('Редактировать фотографии')
            ->setUri($this->generateUrl('monolith_module.gallery.admin'));

        return $this->render('@GalleryModule/latest_widget.html.twig', [
            'photos'  => $photos,
        ]);
    }
}
